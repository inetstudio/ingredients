# Elasticsearch

````
PUT app_index
PUT app_index/_mapping/ingredients
{
  "properties": {
    "id": {
      "type": "integer"
  	},
    "title": {
  	  "type": "string"
    },
	  "description": {
  	  "type": "text"
  	},  
	 "content": {
  	  "type": "text"
  	 },	
    "tags": {
      "type": "nested"
    },
    "products": {
      "type": "nested"
    }
  }
}
````
