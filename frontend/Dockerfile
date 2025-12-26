FROM node:20-alpine

WORKDIR /app

COPY package*.json ./
RUN npm install

EXPOSE 3883

CMD ["npm", "run", "dev", "--", "--host"]
