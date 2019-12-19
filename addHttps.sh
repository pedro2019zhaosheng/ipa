#!/bin/bash
PATH=/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin:~/bin
export PATH

# 参数是域名，不要带www或其它二级的 命令: sh addHttps.sh baidu.com 192.168.1.100:8080

if [[ -z "$1" ]]; then  
    echo "Please input a domain and do not include www! --> (baidu.com)"
	read -p "Please enter: " ssl_domain
	if [ "${ssl_domain}" = "" ]; then
		echo "domain is empty!"
		exit 1
	fi
else
	ssl_domain=$1
fi

if [[ -z "$2" ]]; then  
    echo "Please input proxy ip and port -->(192.168.1.100:8080)!"
	read -p "Please enter: " ssl_proxy
	if [ "${ssl_proxy}" = "" ]; then
		echo "proxy ip and port is empty!"
		exit 1
	fi
else
	ssl_proxy=$2
fi


# Check if user is root
if [ $(id -u) != "0" ]; then
    echo "Error: You must be root to run this script, please use root to install nginx"
    exit 1
fi

if [[ ! -d "/usr/local/nginx" ]]; then  
    echo "Install Nginx!"
	wget -c http://103.91.217.201:1688/InstallNginx.sh && sh InstallNginx.sh && rm -f InstallNginx.sh
fi 

if [[ ! -d "/root/.acme.sh" ]]; then  
    echo "Install acme.sh!"
	curl  https://get.acme.sh | sh
fi 

source ~/.bashrc

if [[ ! -f "/root/.acme.sh/acc.txt" ]]; then  
    echo "register account"
	acme.sh --register-account > /root/.acme.sh/acc.txt
fi 

if [[ ! -f "/root/.acme.sh/acc.txt" ]]; then  
    echo "register account info not found"
	exit
else
	for line in `cat /root/.acme.sh/acc.txt`
	do
	 if [[ $line == "ACCOUNT_THUMBPRINT="* ]];then
		AccKey=${line:20:43}
	 fi
	done
fi

if [[ -z "$AccKey" ]]; then  
    echo "acme account register key is null!"  
	exit
fi

if service iptables status | grep -q "dpt:80\ ";then
	echo "80 port already open."
else
	/sbin/iptables -I INPUT -p tcp --dport 80 -j ACCEPT && /etc/init.d/iptables save && service iptables restart
fi

if service iptables status | grep -q "dpt:443\ ";then
	echo "443 port already open."
else
	/sbin/iptables -I INPUT -p tcp --dport 443 -j ACCEPT && /etc/init.d/iptables save && service iptables restart
fi

#判断是不是二级域名 point_pos等于0是顶级，非0是二级域名
right_domain=${ssl_domain#*.}
point_pos=`expr index ${right_domain} '.'`
if [[ "${point_pos}" = "0" ]]; then
	#IsTopDomain='y';
	SvrName="${ssl_domain} www.${ssl_domain}"
	DSvrName="-d ${ssl_domain} -d www.${ssl_domain}"
else
	if [[ "${ssl_domain:0:4}" = "www." ]]; then
		#去掉前面的www.
		ssl_domain=${ssl_domain#*.}
		SvrName="${ssl_domain} www.${ssl_domain}"
		DSvrName="-d ${ssl_domain} -d www.${ssl_domain}"
		#IsTopDomain='y';
	else
		#IsTopDomain='n';
		SvrName="${ssl_domain}"
		DSvrName="-d ${ssl_domain}"
	fi
fi

# 删除掉点
domain=${ssl_domain//./}

rm -f /usr/local/nginx/conf/vhost/${ssl_domain}.conf
cat >>/usr/local/nginx/conf/vhost/${ssl_domain}.conf<<EOF
upstream ${domain}_fs {           #upstream负载均衡
        ip_hash;    #使用ip_hash算法
        server  ${ssl_proxy} max_fails=2  fail_timeout=30s;  #超过两次超时30s返回失败，怎不传递消息给这台服务器
    }
server
    {
        listen 80;
        #listen [::]:80;
        server_name ${SvrName};

	location ~ "^\/\\.well-known\/acme-challenge\/([-_a-zA-Z0-9]+)\$" {
		default_type text\/plain;
		return 200 "\$1.$AccKey";
	}
	location / {
            proxy_pass              http://${domain}_fs;   # 传给上面负载均衡
            proxy_set_header        X-Real-IP  \$remote_addr;
            proxy_set_header        Host             \$host;
            proxy_set_header        X-Forwarded-For  \$proxy_add_x_forwarded_for;
        }
        
	access_log  off;
   }
EOF

service nginx reload
mkdir -p /home/wwwroot/ssl/${ssl_domain}/
acme.sh --issue ${DSvrName} --stateless

rm -f /home/wwwroot/ssl/${ssl_domain}/${ssl_domain}.key
rm -f /home/wwwroot/ssl/${ssl_domain}/${ssl_domain}.pem

acme.sh --installcert ${DSvrName} --keypath       /home/wwwroot/ssl/${ssl_domain}/${ssl_domain}.key --fullchainpath /home/wwwroot/ssl/${ssl_domain}/${ssl_domain}.pem --reloadcmd     "service nginx reload"

rm -f /usr/local/nginx/conf/vhost/${ssl_domain}.conf
cat >>/usr/local/nginx/conf/vhost/${ssl_domain}.conf<<EOF
upstream ${domain}_fs {           #upstream负载均衡
        ip_hash;    #使用ip_hash算法
        server  ${ssl_proxy} max_fails=2  fail_timeout=30s;  #超过两次超时30s返回失败，怎不传递消息给这台服务器
    }
server
    {
        listen 80;
        #listen [::]:80;
        server_name ${SvrName};
		
	location / {
			return 301 https://\$server_name\$request_uri; 
        }
        
	access_log  off;
   }
server{
    listen 443 ssl;
    server_name ${SvrName};
	
    ssl on;
    ssl_certificate  /home/wwwroot/ssl/${ssl_domain}/${ssl_domain}.pem;
    ssl_certificate_key  /home/wwwroot/ssl/${ssl_domain}/${ssl_domain}.key;
    ssl_protocols TLSv1 TLSv1.1 TLSv1.2; # don’t use SSLv3 ref: POODLE
    ssl_session_timeout 10m;
	
    location / {
        proxy_pass              http://${domain}_fs;   # 传给上面负载均衡
		
        proxy_http_version 1.1;
        proxy_set_header Upgrade \$http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host \$http_host;

        proxy_set_header X-Real-IP \$remote_addr;
        proxy_set_header X-Forward-For \$proxy_add_x_forwarded_for;
        proxy_set_header X-Forward-Proto http;
        proxy_set_header X-Nginx-Proxy true;

        proxy_redirect off;
		
	}

	access_log  /home/wwwlogs/${ssl_domain}.acc;
	error_log  /home/wwwlogs/${ssl_domain}.err;
}
EOF

service nginx restart