## 数据库篇
* MySQL
    * CRUD
    * JOIN、LEFT JOIN 、RIGHT JOIN、INNER JOIN
    * UNION
    * GROUP BY + COUNT + WHERE 组合案例
    * [常用 MySQL 函数，如：now()、md5()、concat()、uuid()等](https://www.w3schools.com/sql/sql_ref_mysql.asp)
    * `1:1`、`1:n`、`n:n` 各自适用场景
    * 了解触发器是什么，说个使用场景
    * 数据库优化手段
        * 索引、联合索引（命中条件）
        * 分库分表（`水平分表`、`垂直分表`）
        * 分区
        * 会使用 `explain` 分析 SQL 性能问题，了解各参数含义
            * 重点理解 `type`、`rows`、`key`
        * Slow Log（有什么用，什么时候需要）
* MSSQL(了解)
    * 查询最新5条数据
* NOSQL
    * Redis、Memcached、MongoDB
    * 对比、适用场景（可从以下维度进行对比）
        * 持久化
        * 支持多钟数据类型
        * 可利用 CPU 多核心
        * 内存淘汰机制
        * 集群 Cluster
        * 支持 SQL
        * 性能对比
        * 支持事务
        * 应用场景
    * 你之前为了解决什么问题使用的什么，为什么选它？

