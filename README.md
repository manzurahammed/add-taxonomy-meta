# Add Taxonomy Meta Plugin

**Contributors:**      [manzurahammed](https://github.com/manzurahammed)
**Tags:**              fields, taxonomy-meta, settings  
**Requires at least:** 3.9.0  
**Tested up to:**      5.3  
**Stable tag:**        1.0.2 
**License:**           GPLv2 or later  
**License URI:**       [http://www.gnu.org/licenses/gpl-2.0.html](http://www.gnu.org/licenses/gpl-2.0.html)

## Description

Add Taxonomy Meta is a developer's toolkit for building taxonomy-meta, taxonomy data, for WordPress that will blow your mind. Easily manage meta for  terms.

## Custom Field Types
1.  ```Text```
2.  ```Select```
3.  ```Image```

## Example
```php
function add_data($data){
	$data['add_data'] =  array(
		'cat_name' =>'category',
		'id'=>'add_data',
		'fields' => array(
			array(
				'label' => esc_html__('State Name','text-domin'),
				'id' => 'state',
				'type' => 'text'
			),
			array(
				'label'=> esc_html__('Country Name','text-domin'),
				'id' => 'country',
				'type' => 'select'
				'value' => array('1'=>'One','2'=>'Two')
			),
			array(
				'label'=> esc_html__('Image','text-domin'),
				'id'=>'image',
				'type'=>'image'
			),
		)
	);
	return $data;
}
add_filter('add_category_meta','add_data');
```
