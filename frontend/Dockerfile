FROM node:20-alpine

WORKDIR /app

COPY package*.json ./
RUN npm install

EXPOSE 3883

COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

CMD ["npm", "run", "dev", "--", "--host"]
