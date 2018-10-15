# ElasticPoSobotaWorkshop

## Prerequisites
- Installed PHP >=7.1.0
- Cloned this repository to your computer
- Run `composer install`
- ElasticSearch in version 6
- Kibana in version 6

### Set up ElasticSearch - Manual
- For ElasticSearch go [here](https://www.elastic.co/downloads/elasticsearch) and follow installation steps. 
- For Kibana go [here](https://www.elastic.co/downloads/kibana) and follow installation steps. 

### Set up ElasticSearch - Docker
To start elasticsearch without installing it, you can use Docker & docker-compose. Just `cd` into this repo and run:

```
docker-compose up
```

The ES will be available on [http://localhost:9200](http://localhost:9200) and Kibana on 
[http://localhost:5601](http://localhost:5601)
 
 
## Result 
![workshop start](https://raw.githubusercontent.com/VBoss/ElasticPosobotaWorkshop/master/www/images/workshop-start.png "Workshop start")
