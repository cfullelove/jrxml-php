#Readme

jrxml-php is a PHP library that allows for the generation of documents based on Jasper Report XML (jrxml) templates.

## Usage

```php
require( "vendor/autoload.php" );

$report = new JasperReport\JasperReport( __DIR__ . "/redbook_roster_published.jrxml" );

$datasource = new JasperReport\Datasource\MysqlDatasource(
	'localhost',
	'user',
	'pass',
	'db',
	3306
);

$params = array(
	'param1' => "value1",
	'param2' => "value2"
);

$pdf = $report->renderReport(
	new JasperReport\OutputAdapter\FPDFOutputAdapter(),
	$datasource, 
	$params
);

file_put_contents( 'filename.pdf', $pdf->output() );

```

##Status
jrxml-php can only handle simple jrxml reports at this time and should be considered alpha stage software.

### Supported Features
- Bands:
	- Page Header
	- Column Header
	- Detail
	- Column Footer
	- Page Footer
- Elements:
	- Static Text
	- Text Field
	- Table
	- Rectangle
- Parameter and Field evaluation
- Pluggable Datasource
- Pluggable OutputAdapter

### Unsupported Features
- Variables ($V{var})
- Scriptlets
- Groups
- Subreports
