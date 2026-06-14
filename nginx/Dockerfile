FROM nginx
COPY ./nginx.conf /etc/nginx/templates/default.conf.template

CMD ["/bin/sh", "-c", "envsubst '${APP_DOMAIN}' < /etc/nginx/templates/default.conf.template > /etc/nginx/conf.d/default.conf && nginx -g 'daemon off;'"]

# Copia los certificados desde el backend hacia el contenedor de Nginx
#COPY ./wildcard/star_idartes_gov_co.crt /etc/nginx/certificates/
#COPY ./wildcard/star_idartes_gov_co.key /etc/nginx/certificates/
#COPY ./wildcard/DigiCertCA.crt /etc/nginx/certificates/
