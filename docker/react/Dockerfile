FROM node:18-alpine

WORKDIR /app

COPY ./frontend/package.json ./
COPY ./frontend .

RUN npm install


EXPOSE 3000

CMD ["npm", "run", "start"]
