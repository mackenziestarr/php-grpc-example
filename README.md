# php-grpc-example

demonstrating gRPC and talking to Google services using mod_php with
MPM prefork.

## Getting Started

These instructions will get you a copy of the project up and running
on your local machine for development and testing purposes.

### Prerequisites

- docker
- a google compute instance with bigtable.reader permissions

### Installing

Build and run the docker image

```
$ ./init.sh # script that wraps building and restarting the containers
$ curl 'localhost:8080/example/hello-world.php' # verify things are working as expected
```

#### Bigtable

The `/example/bigtable.php` endpoint demonstrates bigtable performance from
the context of an apache worker utilizing mod_php and MPM prefork.

```
# export variables for talking to bigtable
$ export PROJECT_ID=<project_id>
$ export INSTANCE_ID=<instance_id>
$ export TABLE_ID=<table_id>
$ export KEY=<key> # a row key from the table

$ curl "localhost:8080/example/bigtable.php?project=$PROJECT_ID&instance=$INSTANCE_ID&table=$TABLE_ID&key=$KEY"
# pid 16    uuid 5d23aad4d8793    key 100086511    value {"value":{...    time 25.44 ms

# specify number of requests to make to bigtable 
$ curl "localhost:8080/example/bigtable.php?project=$PROJECT_ID&instance=$INSTANCE_ID&table=$TABLE_ID&key=$KEY&num_requests=<num_requests>"

# use yac php extension to cache auth token
$ curl "localhost:8080/example/bigtable.php?project=$PROJECT_ID&instance=$INSTANCE_ID&table=$TABLE_ID&key=$KEY&cache_auth=true"
```

##### Authing with Bigtable

Ideally this endpoint should be accessed on a GCE with bigtable.reader
permissions for your instance but if you want to use a service-account
JSON file you can put it in `./secrets` and uncomment the commented
lines in `./init.sh`

#### gRPC Hello World Java server

The `/example/greeter-client.php` endpoint runs the hello world gRPC
example with php as the client and java as the server

```
$ curl localhost:8080/example/greeter-client.php
pid 23    message Hello world    time 4.78 ms
```

### Apache Notes

- You can also change the mpm prefork config by editing the
`config/mpm_prefork.conf` file and running the `./init.sh` script -
You can restart apache2 with `docker exec -it php-grpc-client
apachectl graceful` once the container is running, this can be helpful
for debugging slow gRPC channel initialization
- the hellow
