
API SCRAPER
=========  

1. [О скрапере](#about)
2. [Быстрая установка](#install)
3. [Основные компоненты](#components)
4. [Подстановочные шаблоны](#templates)

<a id="about"></a>
# О скрапере


Приложение предназначено для получения и сохранения данных по api.  Фактически, это настраиваемый api клиент для выгрузки данных.

<a id="install"></a>
# Быстрая установка

При первом запуске:
> make install

При повторных запусках:
> make run

После успешного старта приложение будет доступно по  [этой ссылке](https://localhost:80/home).

<a id="components"></a>
# Основные компоненты:

- **Data Schema**  \
  Схема данных представляет собой шаблон, по которому будет сформирован запрос и прочитан ответ.  
  Схемы данных могут быть связаны в рамках одной группы, в таком случае они будут объеденины  
  в инструкцию. На настоящий момент поддерживается связь 1:1, то есть, одна схема может выступать  
  *внешним источником данных* для одной единственной схемы.

  Пример: запрос из первой схемы возвращает json со списком автомобилей. Каждый объект автомобиля  
  имеет поля id, firm, model, price. Однако, мы хотим узнать телефонный номер владельца авто, и для   
  его получения в целевом апи имеется отдельный метод, например, /<car_id>/contacts.  
  То есть, для получения номера телефона нужно знать id автомобиля.

  Скрапер поддерживает связанные запросы - в таком случае нужно создать две схемы: для получения списка автомобилей, и  
  получения контактных данных. Схемы объединятся в инструкцию: сначала будет получен список авто, затем для каждого авто будет  
  выполнен запрос на получение контактных данных.

- **Group**  \
  Группа объединяет пулл связанных схем. Исходя из группы формируется инструкция со связанными запросами. Также  
  группа нужна для правильного формирования Output Schema

- **Output Schema**  \
  Шаблон, позволяющий выбрать перечень Response Field. Согласно этой схеме парсятся данные, полученные в результате выполнения  
  инструкции с Data Schemas. Должен принадлежать той же группе, что и целевые Response Field.

- **Request Field**  \
  Обычное поле запроса. Единственная особенность - поддержка динамической подстановки значений по шаблонам.  
  Поле запроса можно привязать ко внешнему источнику. Для этого в качестве значения поля нужно указать  
  путь к целевому значению в формате *path.to.\*.target.value*. В таком случае сначала будет исполнен внешний запрос,  
  значение из которого будет подставлено в текущий.
  Если одному ключу соответствувет несколько значений, нужно создать поле для каждого значения. Формат ключа поля в таком случае должен быть  
  *<key_name>[]*.

- **Response Field**  \
  Предоставляет информацию о том, как именно нужно парсить полученный ответ. Для этого нужно указать  
  путь к целевому значению в теле ответа по точечной нотации: *path.to.target.*.value*, где символ * означает массив.  
  Если скрапер встречает этот символ, то текущий запрос будет выполнен с подстановкой значения для каждого элемента массива во внешнем источнике.

  На данный момент конечным значением пути может быть только простое значение, массив не поддерживается. Однако можно получить определенный элемент  
  массива, например *path.to.target.*.value.<index>*.

  Также доступна пост-обработка ответа, по аналогии с подстановкой значений в Request Field.


<a id="templates"></a>
# Подстановочные шаблоны

Схемы данных поддерживают динамическую подстановку значений посредством шаблонных строк:


## Request Field:

Шаблоны могут применяться как к ключу, так и к значению.

**Таймстемп**
>{{:timestamp}}

- локация: value

Подстановка текущего времени

**Индекс пагинации**


>{{:page}}

- локация: value

Инкремент страницы для каждой итерации

**Загрузка внешнего значения в url-параметр**

>{{:url_parameter=<parameter_name>}}
- локация: value, url
- параметр: parameter_name

<parameter_name>  должен быть указан в качестве ключа Request Field и сегмента в поле url.  
При этом в качестве значения Request Field выступает поле из внешней Data Schema.

Параметр <parameter_name> может быть произвольным, однако он используется при формировании подписи запроса (сортировка параметров по ключу).

**Подпись запроса**
>{{:secret}}

- локация: value

На данный момент реализована поддержка подписи запроса для api.drom.ru. Если указан, при инициализации   
парсинга нужно указать соль, подмешиваемую к хэшу запроса.

**Генерация случайной строки фиксированной длины**
>{{:random_string=<string_length>}}

- локация: value
- параметр: string_length

---  

## Response Field
Эти шаблоны могут применяться только полю name для Response Field.

**Загрузка изобажения**\
>{{:image=<directory_key>}}>

- локация: name

Позволяет загружать изображения из полей ответа. В качестве параметра нужно указать поле ответа, значение которого будет использовано для  
имени поддиректории, в которую сохранится изображение.


# Примечания

n/a