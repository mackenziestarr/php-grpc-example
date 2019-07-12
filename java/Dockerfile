FROM gradle:jdk8

# install git
RUN apt-get update && \
    apt-get install -y --no-install-recommends git

RUN git clone -b v1.21.0 https://github.com/grpc/grpc-java.git

WORKDIR grpc-java/examples

RUN ./gradlew installDist

ENTRYPOINT ./build/install/examples/bin/hello-world-server

