# SORM (Simple Object-Relational Mapping)

### Начало работы и настройка

> Важно! На данный момент система способна работать только с MySQL/MariaDB.

```
// Устанавливаем драйвер и конфигурацию
// Первый параметр "mysql" может быть произвольным, это просто наименование драйвера, чтобы на него потом можно было переключиться
// Второй параметр является путём к файлу конфигурации
DataSource::setup('mysql', include('App' . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'mysql.php'));
// Устанавливаем драйвер, как текущий (драйверов может быть несколько, поэтому нужно установить текущий драйвер, чтобы система знала откуда брать данные)
// При необходимости дайвер может быть переключён прямо в процессе работы приложения
DataSource::setCurrent('mysql');
```

#### Содержимое файла конфигурации

```
<?php
return [
    'driver' => 'MySQL', // Имя драйвера
    'host' => '127.0.0.1',
    'user' => 'db_user_name',
    'password' => 'password',
    'db' => 'db_name'
];
```



### Описание моделей 

Модель описывается путём наследования от базового класса Entity и указания имени таблицы БД. 
Все поля система получает автоматически. Для автокомплита нужно оформить phpDoc.

```
/**
  * Категория
  * 
  * @property int $id
  * @property string $name
  */
class Category extends Entity 
{
  
  protected $tableName = 'category_table_name';
  
}

/**
  * Элемент категории
  *
  * @property int $id
  * @property string $name
  * @property int $category_id
  */
class Item extends Entity 
{
  
  protected $tableName = 'category_item_table_name';
  
}
```



### Описание связей моделей

Связи устанавливаются напрямую в моделях. Для этого нужно создать в модели методы с произвольной сигнатурой.
Например, установим связи между моделями "Категория" и "Элемент категории":

```
/**
  * Категория
  * 
  * @property int $id
  * @property string $name
  */
class Category extends Entity 
{
  
  protected $tableName = 'category_table_name';
  
  public function getItems() 
  {
    /** @var Item[] $aItems */
    $aItems = $this->findRelationCache('id', Item::cls()); // Сначала ищем кэшированные данные
    if (empty($aItems)) { // Если кэш пуст, то подгружаем данные из БД
      $oItems = DataSource::factory(Item::cls()); // Создаём пустой объект модели "Элемента категории"
      $oItems->builder()->where("category_id={$this->id}"); // Устанавливаем условия поиска данных
      
      $aItems = $oItems->findAll(); // Получаем данные из БД
      // Теперь нужно кэшировать полученные данные
      // (не обязательно, но если этого не сделать, то система будет каждый раз запрашивать данные из БД)
      foreach ($aItems as $oItem) {
        $this->addRelationCache('id', $oItem); // Кэшируем полученные "Элементы категории" в "Категории" по первичному ключу
        $oItem->addRelationCache('category_id', $this); // Кэшируем "Категорию" в полученном "Элементе категории" по внешнему ключу
      }
    }
    
    return $aItems;
  }
  
}

/**
  * Элемент категории
  *
  * @property int $id
  * @property string $name
  * @property int $category_id
  */
class Item extends Entity 
{
  
  protected $tableName = 'category_item_table_name';
  
  public function getCateory() 
  {
    /** @var Category[] $aCategories */
    $aCategories = $this->findRelationCache('id', Category::cls()); // Сначала ищем кэшированные данные
    if (empty($aCategories)) { // Если кэш пуст, то подгружаем данные из БД
      $oCategories = DataSource::factory(Category::cls()); // Создаём пустой объект модели "Категория"
      $oCategories->builder()->where("id={$this->category_id}"); // Устанавливаем условия поиска данных
      
      $aCategories = $oCategories->findAll(); // Получаем данные из БД
      // Теперь нужно кэшировать полученные данные
      // (не обязательно, но если этого не сделать, то система будет каждый раз запрашивать данные из БД)
      foreach ($aItems as $oItem) {
        $this->addRelationCache('id', $oItem); // Кэшируем полученную "Категорию" в "Элементе категории" по первичному ключу
        $oItem->addRelationCache('category_id', $this); // Кэшируем "Элемент категории" в полученной "Категории" по внешнему ключу
      }
    }
    
    return isset($aCategories[0]) ? $aCategories[0] : null;
  }
  
}
```



### Пример работы

В описании связей используется класс DataSource и его метод factory(). Вся работа по созданию объектов модели ведётся через этот метод.
Например, чтобы создать создать новый объект модели "Категория" нужно написать такой код:

```
/** @var Category $oCategory */
$oCategory = DataSource::factory(Category::cls()); // Создать новый пустой объект модели
$oCategory->name = 'Моя тестовая категория'; // Заполняем объект данными
$oCategory->commit(); // Сохраняем объект (в БД будет добавлена новая запись).
```

Для того, чтобы получить уже существующий объект нужно написать такой код:

```
/** @var Category $oCategory */
$oCategory = DataSource::factory(Category::cls(), 1); // Загружаем из БД объект с первичным ключем равным 1.
echo $oCategory->name; // Выводим название категории
$oCategory->name = 'Изменённое название категории'; // При необходимости изменяем данные объекта
$oCategory->commit(); // Сохраняем объект, если были изменения (В БД будут отредактированы данные записи с первычным ключем равным 1)
```

Теперь модели готовы к работе со связями и можно приступать:

```
/** @var Category $oCategory */
$oCategory = DataSource::factory(Category::cls(), 1); // Загружаем категорию с первичным ключем равным 1
$aItems = $oCategory->getItems(); // Получаем по связям все элементы категории, которые принадлежат данной категории
```



### Выполнение произвольных запросов

> Внимание! Извлечение данных очищает результаты выполнения запроса, поэтому извлечь данные можно только 1 раз после выполнения запроса.
> Если необходимо извлечь данные ещё раз, то нужно повторить запрос к БД.

#### Извлечь данные в виде массива

```
$currentDriver = DataSource::getCurrent(); // Получаем драйвер
$currentDriver->query('select * from table_name'); // Выполняем запрос
$result = $currentDriver->fetchAll(); // Извлекаем результаты в виде массива
```

#### Извлечь данные в виде ассоциативного массива

```
$currentDriver = DataSource::getCurrent(); // Получаем драйвер
$currentDriver->query('select * from table_name'); // Выполняем запрос
$result = $currentDriver->fetchAssoc(); // Извлекаем результаты в виде ассоциативного массива
```

#### Построковое извлечение данных

```
$currentDriver = DataSource::getCurrent(); // Получаем драйвер
$currentDriver->query('select * from table_name'); // Выполняем запрос
$result = $currentDriver->fetchRow(); // Извлекаем первую строку результатов
```

#### Получение списка полей, полученных в результате выполнения запроса

```
$currentDriver = DataSource::getCurrent(); // Получаем драйвер
$currentDriver->query('select * from table_name'); // Выполняем запрос
$result = $currentDriver->fetchFields(); // Получаем список полей результата
```



### Построитель запросов

В системе существует 4 типа построителей запросов: Select, Update, Delete, Insert;

#### Построитель Select

> Применяется для формирования запроса на получение данных из БД.

