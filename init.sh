#!/bin/bash

# create a shared network so the two containers can talk to each other
docker network create grpc-test

# [php-grpc-client]
docker rm  -f   php-grpc-client
docker build -t php-grpc-client .
# note: uncomment commented lines below if you are using a service account json file
docker run -d -p8080:80                \
  --volume $(pwd)/src:/var/www/html/example/  \
  --network grpc-test                    \
  --name php-grpc-client php-grpc-client \
#  -v $(pwd)/secret:/secret \
#  -e "GOOGLE_APPLICATION_CREDENTIALS=/secret/<your-service-account-key>.json"

# [java-grpc-server]
docker rm -f    java-grpc-server
docker build -t java-grpc-server java/.
docker run -d -p50051:50051 \
       --network=grpc-test  \
       --name java-grpc-server java-grpc-server \
