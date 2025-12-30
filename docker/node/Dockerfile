FROM node:alpine

RUN mkdir -p /usr/src/app
RUN mkdir -p /usr/src/app/node_modules
WORKDIR /usr/src/app

COPY ./package.json /usr/src/app/
RUN npm install
RUN chmod -R 777 /usr/src/app/node_modules/