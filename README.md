# dnmp_fast
dnmp fast 版本


[国内Gitee地址](https://gitee.com/ltxlong/dnmp_fast)

```
  dnmp_fast
  ├── services                -- 服务目录
  ├── www                     -- 项目目录
  ├── env                     -- 环境配置文件
  ├── docker-compose.yml      -- 基础配置文件

```
 
# dnmp fast 版本和dnmp版本的区别：
fast版本的php扩展是离线安装的，不用再在线去下，在网络不好或者众所周知的原因，下载php扩展会很慢很卡

fast版本和dnmp版本一样，php镜像默认是基于php:x-fpm-alpine，但fast版本额外提供了非alpine的选择php:x-fpm

# Linux环境依赖：
docker
docker-compose

# Window环境依赖：
Docker Desktop


# 运行前要修改的配置：
1、php项目代码路径，在.env修改，默认是./www
2、php.ini需不需要开启yasd扩展
3、redis的密码设置（redis.conf）
4、mysql的端口
5、redis的端口
6、其他配置，如nginx配置

# docker一些命令
```
# 列出本地镜像
docker images

# docker删除命令
# 删除镜像
docker rmi 镜像id/名称

# 强制删除镜像
docker rmi -f 镜像id/名称

# 删除所有本地镜像
docker rmi $(docker images -q)

# 停用全部运行中的容器
docker stop $(docker ps -q)
 
# 删除所有未被 tag 标记和未被容器使用的镜像
docker image prune

# 删除所有停止运行的容器
docker container prune

# 查看启动的服务
docker ps

# 删除 docker 所有资源
docker system prune

# 删除所有未被挂载的卷
docker volume prune

# 删除所有网络
docker network prune

# 进入容器
docker exec -it php /bin/sh

# 查看docker运行日志
docker logs mysql

```
```
# 编排启动服务
docker-compose up -d

# 如果要自动构建镜像，要加--build
docker-compose up --build -d

# 编排关闭服务
docker-compose down

```

# 一个win本地成功实践：
phpstudy + docker desktop
用phpstudy配置运行一般的项目
用docker配置运行swoole的项目
- mysql和redis用phpstudy的，这个时候docker的mysql和redis端口要改下
- phpstudy的nginx的端口也要改下
- 所有的项目都放在phpstudy的www里，docker的项目目录指向phpstudy的www就行
- 项目配置的ip是window的ip（mysql和redis的ip都是这样配置），而不是127.0.0.1
- phpstudy的mysql默认只能本地连的，打开mysql库，里面的user表，修改user=root的Host为%，最后执行查询flush privileges;
- phpstudy的redis记得设置密码，不然也连接失败
- phpstudy的redis记得设置bind的ip为0.0.0.0，不然也连接不上

注意的是，docker desktop要先pull基础镜像，再执行docker-compose up --build -d，否则会执行失败
这个问题是因为php镜像不是基础的镜像，而是加了很多的命令
所以要先pull基础镜像，如docker pull php:7.4-fpm-alpine





