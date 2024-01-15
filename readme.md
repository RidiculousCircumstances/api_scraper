API SCRAPER
=========

# Подстановочные шаблоны

---

**Таймстемп**\
{{:timestamp}}

**Индекс пагинации**\
{{:page}}

**Загрузка внешнего значения в url параметр**\
{{:url_parameter=<parameter_name>}}

**Подпись запроса**\
{{:secret}}\

**Генерация случайной строки фиксированной длины**\
{{:random_string=<string_length>}}



**Загрузка изобажения**
{{:image=<directory_key>}}>
---


Поле запроса можно привязать ко внешнему источнику. Для этого в качестве значения поля нужно указать
путь к целевому значению в формате *path.to.\*.target.value*. В таком случае сначала будет исполнен внешний запрос,
значение из которого будет подставлено в текущий.