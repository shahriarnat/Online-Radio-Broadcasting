#FROM node:20-alpine AS development-dependencies-env
#COPY ./player/ /app
#WORKDIR /app
#RUN npm ci

FROM node:20-alpine AS production-dependencies-env
COPY ./player /app
WORKDIR /app
RUN npm ci --omit=dev
RUN npm run build

#FROM node:20-alpine AS build-env
#COPY ./player/ /app/
#COPY --from=development-dependencies-env /app/node_modules /app/node_modules
#WORKDIR /app
#RUN npm run build

FROM node:20-alpine
COPY --from=production-dependencies-env /app /app
WORKDIR /app
CMD ["npm", "run", "start"]
