es-tbk
es官网：https://www.easyswoole.com/

测试版本 只实现了简单的领券购买返利

docker镜像 docker pull ymboom/es_tbk

运行 docker run -itd -p9501:9501 --name es_tbk ymboom/es_tbk

进入容器 docker exec -it es_tbk bash

启动easyswoole服务 php easyswoole start

浏览器访问 ip:9501

接口地址 具体查看路由文件 ip:9501/api/action

注：需要先配置 App/Conf