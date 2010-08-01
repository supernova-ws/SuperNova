Коды логов (таблица logs):
  0 - код по умолчанию

1xx - информационные коды
100 - Запуск обновления статистики игроков
101 - Обновление статистики игроков выполнено успешно
102 - Изменение количества Темной Материи

2xx - удачная операция
200 OK
201 Created
202 Accepted
203 Non-Authoritative Information
204 No Content
205 Reset Content
206 Partial Content

3xx - Предупреждения системы логов
300 - Возможный багоюз.  Когда-то  данное  действие  пользователя  приводило  к
      ошибке, дающей ему преимущество в игре (удвоение  флота,  бесплатная  или
      моментальная постройка  итд).  Сейчас  эта  ошибка  устранена,  но  стоит
      присмотрется к пользователю - возможно он багоюзер или хакер
302 - Попытка взлома. Пользователь передал серверу  данные,  не  совпадающие  с
      реальными (например - другой ID пользователя  вместо  своего,  другой  ID
      альянса, вместо своего итд). Обычно это означает  попытку  взлома.  Очень
      редко это может означать о наличии ошибки в коде игры
302 Found
303 See Other
304 Not Modified
305 Use Proxy
306 (Unused)
307 Temporary Redirect

4xx - ошибки при запросе клиента
400 Bad Request
401 - Unauthorized. Пользователь  попытался  получить  доступ  к  части  сайта,
      не доступной без авторизации
102 - Ошибка изменения количества Темной Материи
403 Forbidden
404 Not Found
405 Method Not Allowed
406 Not Acceptable
407 Proxy Authentication Required
408 Request Timeout
409 Conflict
410 Gone
411 Length Required
412 Precondition Failed
413 Request Entity Too Large
414 Request-URI Too Long
415 Unsupported Media Type
416 Requested Range Not Satisfiable
417 Expectation Failed

5xx - ошибки сервера. Это означает сбой в БД или ошибки в коде сервера
500 - У игрока отрицательное количество ресурсов
501 Not Implemented
502 Bad Gateway
503 Service Unavailable
504 Gateway Timeout
505 HTTP Version Not Supported
