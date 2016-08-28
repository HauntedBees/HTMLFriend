# TemplateFriend - Easy HTML Templates in PHP
**TemplateFriend** is a PHP Class that lets you to keep your PHP and HTML separate until you need to combine them. 

## Setup
Just drop the PHP file in your project and include it in the files you need it in!

```php
require_once "TemplateManager/TemplateClass.php";
```

## Usage
First you need some HTML templates, so let's say you have a file **template.html** that looks like this:
```html
<!DOCTYPE html>
<html>
	<head>
		<title>{@title}</title>
	</head>
	<body>Hello there {@name}!</body>
</html>
```

Now if you want to turn this into something, you'd write some PHP like this:
```php
$t = new Template("template.html");
$t->SetKeys(["title" => "Test Page", "name" => "Kevin"];
echo $t->GetContent();
```

The end result would be this HTML:
```html
<!DOCTYPE html>
<html>
	<head>
		<title>Test Page</title>
	</head>
	<body>Hello there Kevin!</body>
</html>
```

And that's it for the basics! Just pass an array of key value pairs to ```SetKeys``` and it will turn each instance of ```{@key}``` in the specified HTML file into ```value```!

Additionally, there are a few other functions to make a few things easier:

### SetKey
Adds a single key value pair to the template, which will be replaced when ```GetContent``` is called. The below code is functionally identical to the previous example.
```php
$t = new Template("template.html");
$t->SetKey("title", "Test Page");
$t->SetKey("name", "Kevin");
echo $t->GetContent();
```

### ClearKeys
Clears the key value pairs array so the template can be reused without having to create a new **Template** object for the same HTML file.

### GetLoopedContent
Takes a key value array as input and returns the formatted HTML. This is the same as calling ```ClearKeys```, ```SetKeys``` and ```GetContent``` all in a row. You can use it for quick reuse of the template, but it's mostly used internally by the following two functions:

### GetForEachContent
Takes a value array, a function, and an arguments array as input. The output will be the formatted template, repeated for each value in the array. For example, for an html file **looptest.html**:
```html
<li>{@value}</li>
```
This code can be used:
```php
$note = "Remember to buy ";
$arr = ["beans", "eggs", "grapefruit"];
$t = new Template("looptest.html");
echo $t->GetForEachContent($arr, function($elem, $args) {
	return ["value" => $args["note"].$elem];
}, ["note" => $note]);
```

The output of this would be the following HTML:
```html
<li>Remember to buy beans</li>
<li>Remember to buy eggs</li>
<li>Remember to buy grapefruit</li>
```

Essentially, the function passed to ```GetForEachContent``` will be ran on each element in the array, with the third ```$args``` argument also being passed to the function, returning a key value pairs array that will be used by ```GetContent``` to fill in the template. For simple loops, you won't even need the ```$args``` object and your function body will probably just look something like ```return ["value" => $elem];``` .

### GetPDOFetchAssocContent
This behaves the same as ```GetForEachContent```, but instead of looping through an array, it takes an executed PDO command as input and iterates through the rows of the data table.
```php
$pdo = new PDO("mysql:host=HOSTNAME;dbname=DBNAME", "USERNAME", "PASSWORD");
$STH = $pdo->prepare("SELECT name, age FROM people WHERE hometown = :a");
$STH->execute(["a" => "Santa Barbara"]);
$t = new Template("peopleListing.html");
echo $t->GetPDOFetchAssocContent($STH, function($row, $args) {
	return [
    	"name" => $row["name"], 
        "age" => $row["age"], 
        "yearOrYears" => ($row["age"] == "1" ? "year" : "years")
	];
});
```

If **peopleListing.html** looks like this:
```html
<li>{@name} is {@age} {@yearOrYears} old.</li>
```

...then the above code may return something like this:
```html
<li>Bob is 10 years old.</li>
<li>Susan is 15 years old.</li>
<li>Craig is 49 years old.</li>
<li>Baby is 1 year old.</li>
```

You can see that with the ```{@yearOrYears}``` value up there, passing a function allows more flexibility than just a simple 1:1 mapping of template keys to table columns.

Additionally, you can pass an integer variable by reference as the final argument and it will be populated with the number of rows that were processed.

## Why Not Combine Them?

Of course, there's no reason you can't combine multiple templates! Mix and match!

**ListItem.html**
```html
<li class="{@class}">{@value}</li>
```
**OuterList.html**
```html
<ul>
	{@items}
</ul>
```
**Container.html**
```html
<!DOCTYPE html>
<html>
	<head>
		<title>{@title}</title>
	</head>
	<body>
    	{@contents}
	</body>
</html>
```
**PHP**
```php
$main = new Template("Container.html");
$list = new Template("OuterList.html");
$activeColor = "blue";
$listContents = (new Template("ListItem.html"))->GetForEachContent(["red", "blue", "green"], function($elem, $args) {
	return [
    	"value" => $elem, 
        "class" => (($args["active"] == $elem) ? "active" : "")
    ];
}, ["active" => $activeColor]);
$list->SetKey("items", $listContents);
$main->SetKeys([
	"title" => "Multiple Templates!", 
    "contents" => $list->GetContent()
];
echo $main->GetContent();
```

**Output**
```html
<!DOCTYPE html>
<html>
	<head>
		<title>Multiple Templates</title>
	</head>
	<body>
    	<ul>
        	<li class="">red</li>
            <li class="active">blue</li>
            <li class="">green</li>
        </ul>
	</body>
</html>
```

## License
**TemplateFriend** is licensed [GNU GPLv3](https://www.gnu.org/licenses/gpl-3.0.en.html) because sharing is caring.

## Who Dares?
**TemplateFriend** was created by [Sean Finch](http://hauntedbees.com) and is used in [some of his web-based projects](https://github.com/HauntedBees?tab=repositories).