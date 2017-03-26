#+title: MySQL监控与分析
#+author: typefo
#+email: typefo@qq.com
#+options: ^:nil \n:t creator:nil
#+language: zh-CN

#+html_head: <link href="http://cdn.bootcss.com/bootstrap/2.3.2/css/bootstrap.min.css" rel="stylesheet">
#+html_head: <style type="text/css">body{padding:15px;margin:0 auto;width:1024px;font-size:15px;line-height:24px}</style>
#+html_head: <style type="text/css">h1{font-size:28px} h2{font-size:24px} h3{font-size:18px} h4{font-size:16px}</style>
#+html_head: <style type="text/css">p{text-indent:10px}</style>

** 查看所有会话连接信息
   
   查看当前系统中所有会话连接信息

   #+BEGIN_EXAMPLE
   MySQL> show full processlist;
   #+END_EXAMPLE

   Id 表示当前会话 ID 标识，User 表示连接登录的用户， Host 远程主机地址， db 当前连接的数据库， Time 会话连接时长， Info 正在执行的命令， Progress 表示 SQL 耗时时间。当发现某一个会话耗时太长，可以使用 kill 命令 + 会话ID，来强制终止会话。

   #+BEGIN_EXAMPLE
   MySQL> kill 12
   #+END_EXAMPLE

** 使用慢查询日志记录耗时操作

   数据库在使用过程中，有时会出现一些 SQL 语句执行太慢，启用 MySQL 的慢查询日志，可以记录所有耗时的 SQL 查询，以便做性能分析和优化。

   #+BEGIN_EXAMPLE
   MySQL> show variables like 'slow_query_log';
   #+END_EXAMPLE

   上面查询 slow_query_log 值为 OFF 就表示没有启用慢查询日志。可以在 MySQL 运行期间动态开启慢查询日志记录耗时 SQL 查询，如下

   #+BEGIN_EXAMPLE
   MySQL> set global slow_query_log = ON;
   MySQL> set global long_query_time = 1;
   #+END_EXAMPLE

   也可在 my.cnf 配置文件中加入下列选项，可以永久生效

   #+BEGIN_EXAMPLE
   [mysqld]
   slow_query_log
   long_query_time = 3
   log_queries_not_using_indexes
   slow_query_log_file = /var/lib/mysql/slowquery.log

   #+END_EXAMPLE

   log_slow_queries 表示慢查询日志件，long_query_time 表示执行时间超过 3 秒的 SQL 查询将被记录。log_queries_not_using_indexes 表示没有用到索引的 SQL 查询也将被记录。

** 使用 mysqldumpslow 分析慢查询日志
   
   mysqldumpslow 工具可以自动分析和排序 MySQL 的慢查询日志文件，基本使用格式如下
   
   #+BEGIN_EXAMPLE
   mysqldumpslow [option] files
   #+END_EXAMPLE
   
   输出执行最多次的前 10 个 SQL 查询

   #+BEGIN_EXAMPLE
   $ mysqldumpslow -s c -t 10 /var/lib/mysql/slowquery.log
   #+END_EXAMPLE

   输出查询时间最长的前 10 个 SQL 查询
   
   #+BEGIN_EXAMPLE
   $ mysqldumpslow -s t -t 10 /var/lib/mysql/slowquery.log
   #+END_EXAMPLE
 
   输出返回数据记录数最多的前 10 个 SQL 查询

   #+BEGIN_EXAMPLE
   $ mysqldumpslow -s r -t 10 /var/lib/mysql/slowquery.log
   #+END_EXAMPLE

   输出按查询时间排序里面包含 count 关键字语句的 SQL 查询
   
   #+BEGIN_EXAMPLE
   $ mysqldumpslow -s t -t 10 -g 'count' /var/lib/mysql/slowquery.log
   #+END_EXAMPLE

** 查看当前线程运行数

   使用下面这个命令可以查看当前 MySQL 服务器的线程数量

   #+BEGIN_EXAMPLE
   MySQL> show global status like '%threads_runn%';
   #+END_EXAMPLE

** 查看 MySQL 表锁争用情况

   查看 table_locks_waited 和 table_locks_immediate 两个状态值，如果  table_locks_waited 的值比较高，说明有严重的表锁争用

   #+BEGIN_EXAMPLE
   mysql> show status like 'table%';
   #+END_EXAMPLE

** 查看 InnoDB 表锁争用情况

   查看 MySQL 的 InnoDB 表的锁争用情况，如果 Innodb_row_lock_waits 和 Innodb_row_lock_time_avg 值比较高，说明有严重锁争用
   
   #+BEGIN_EXAMPLE
   mysql> show status like 'innodb_row_lock%';
   #+END_EXAMPLE

