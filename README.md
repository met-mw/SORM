[![Build Status](https://travis-ci.org/met-mw/SORM.svg?branch=master)](https://travis-ci.org/met-mw/SORM)
[![Coverage Status](https://coveralls.io/repos/github/met-mw/SORM/badge.svg?branch=master)](https://coveralls.io/github/met-mw/SORM?branch=master)
[![Latest Stable Version](https://poser.pugx.org/met_mw/sorm/v/stable)](https://packagist.org/packages/met_mw/sorm)
[![Latest Unstable Version](https://poser.pugx.org/met_mw/sorm/v/unstable)](https://packagist.org/packages/met_mw/sorm)
[![Total Downloads](https://poser.pugx.org/met_mw/sorm/downloads)](https://packagist.org/packages/met_mw/sorm)
[![License](https://poser.pugx.org/met_mw/sorm/license)](https://packagist.org/packages/met_mw/sorm)
# SORM - Simple ORM (Object-Relational Mapping)

## Getting started
> Attention! It's works with MySQL/MariaDB only.

### Install
```
composer require met_mw/sorm
```

### Start
> DataSource::i() - Get data source instance.
> DataSource::d() - Get current driver.

```
// Add new driver (MySQL)
DataSource::i()->addDriver('mysql', new Mysql('127.0.0.1', 'db_name', 'charset', 'root', 'password'));
// Set current driver (if added only one driver, then it already current driver)
DataSource::i()->setCurrentDriver('mysql');
DataSource::d()->connect();
```

### Models 
The model is extended from the base class Entity and specify the database table name.
```
/**
  * FirstModel
  * 
  * @property int $id
  * @property string $name
  */
class FirstModel extends Entity 
{
    protected $tableName = 'first_model_table_name';
}

/**
  * FirstModelChild
  *
  * @property int $id
  * @property string $name
  * @property int $first_model_id
  */
class FirstModelChild extends Entity 
{
    protected $tableName = 'fist_model_child_table_name';
}
```

### Relations
Relations are realized through the public model methods.

```
/**
  * FirstModel
  * 
  * @property int $id
  * @property string $name
  */
class FirstModel extends Entity 
{
    protected $tableName = 'first_model_table_name';
  
    public function getFirstModelChilds() 
    {
        /** @var FirstModelChild[] $aFirstModelChilds */
        $aFirstModelChilds = $this->findRelationCache('id', FirstModelChild::cls()); // Search in cache
        if (empty($aFirstModelChilds)) { // If no cache, then load data from data source
            $oFirstModelChilds = DataSource::i()->factory(FirstModelChild::cls()); // Create empty model
            $oFirstModelChilds->getQueryBuilder()
                ->where('first_model_id', '=',  $this->id); // Build search conditions
            $aFirstModelChilds = $oFirstModelChilds->loadAll();
            
            // Caching data
            foreach ($aFirstModelChilds as $oFirstModelChild) {
                $this->addRelationCache('id', $oFirstModelChild);
                $oFirstModelChild->addRelationCache('first_model_id', $this);
            }
        }
        
        return $aFirstModelChilds;
    }
}
```

```
/**
  * FirstModelChild
  *
  * @property int $id
  * @property string $name
  * @property int $first_model_id
  */
class FirstModelChild extends Entity 
{
    protected $tableName = 'fist_model_child_table_name';
  
    public function getFirstModel() 
    {
        /** @var FirstModel[] $aFirstModels */
        $aFirstModels = $this->findRelationCache('id', FirstModel::cls()); // Search in cache
        if (empty($aFirstModels)) { // If no cache, then load data from data source
            $oFirstModels = DataSource::i()->factory(FirstModel::cls()); // Create empty model
            $oFirstModels->getQueryBuilder()
                ->where('id', '=', $this->first_model_id); // Build search conditions
            $aFirstModels = $oFirstModels->loadAll();
      
            // Caching data
            foreach ($aFirstModels as $oFirstModel) {
                $this->addRelationCache('first_model_id $oFirstModel);
                $oFirstModel->addRelationCache('id', $this);
            }
        }
    
        return isset($aFirstModels[0]) ? $aFirstModels[0] : null;
    }
}
```

### Example

#### Create new entity
```
/** @var FirstModel $oFirstModel */
$oFirstModel = DataSource::i()->factory(FirstModel::cls()); // Create empty entity
$oFirstModel->name = 'Test name'; // Set entity data
$oFirstModel->save(); // Save entity data into data source
```

#### Load entity
```
/** @var FirstModel $oFirstModel */
$oFirstModel = DataSource::i()->factory(FirstModel::cls(), 1); // Load entity with Pk value 1
echo $oFirstModel->name; // Any use data
$oFirstModel->name = 'New test name'; // Change data
$oFirstModel->save(); // Save entity data into data source
```

#### Using relations
```
/** @var FirstModel $oFirstModel */
$oFirstModel = DataSource::i()->factory(FirstModel::cls(), 1); // Load entity with Pk value 1
$aFirstModelChilds = $oFirstModel->getFirstModelChilds(); // Load related entities
```

#### Free queries

##### Get data as array
```
DataSource::d()->query('select * from `table_name`'); // Execute query
$result = DataSource::d()->fetchAll(); // Fetch all data as array
```

##### Get data as assoc array
```
DataSource::d()->query('select * from `table_name`'); // Execute query
$result = DataSource::d()->fetchAllAssoc(); // Fetch all data as assoc array
```

##### Get data row as array
```
DataSource::d()->query('select * from `table_name`'); // Execute query
$result = DataSource::d()->fetchRow(); // Fetch data row as array
```

##### Get data row as assoc array

```
DataSource::d()->query('select * from `table_name`'); // Execute query
$result = DataSource::d()->fetchRowAssoc(); // Fetch data row as assoc array
```

## License
The met-mw/SORM package is open-sourced software licensed under the **[MIT license](https://opensource.org/licenses/MIT)**