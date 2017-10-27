<?php

class Process
{
	public $_xml;
    public $path;

    public function __construct()
    {
        $this->_xml = simplexml_load_file("data/data.xml");

    }

    public function getXml()
    {
        return $this->_xml;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }

public function more($id)
{
	if($id == 0) $_SESSION['bread_crumbs'] = '';
	$x = $this->_xml->xpath('row[id="' . $id . '"]');
	$_SESSION['bread_crumbs'] = '';
	$this->bread_crumbs($id);
	echo '<hr>';
	echo nl2br($x[0]->text);
}

public function display($id)
{
	include('header.php');

	if($id == 0) $_SESSION['bread_crumbs'] = '';
	$x = $this->_xml->xpath('row[parent_id="' . $id . '"]');
	$this->bread_crumbs($id);
	echo '<hr>';
	echo '<table>';

	
	foreach($x as $row)
	{
		echo '<tr>';
		
		echo '<td>';
		echo '<a href="process.php?display='.$row->id.'">'.substr($row->text,0,75).'</a>';
		echo '</td>';
		
		echo '<td>';
		echo '<a class="a-green" href="process.php?edit='.$row->id.'">edit</a>';
		echo '</td>';
		
		echo '<td>';
		echo '<a class="a-yellow" href="process.php?more='.$row->id.'">more</a>';
		echo '</td>';
		
		echo '</tr>';
		
	}
	
	echo '<form method="POST" action="process.php?add">';
	echo '<input type="hidden" name="id" value="'. $id.'"/>';
	echo '<textarea cols=45 name="text" ></textarea>';
	echo '<script src="dist/autosize.js"></script>';
	echo '<script>autosize(document.querySelectorAll("textarea"));</script>';	
	echo '<input name="" type="submit" value="add">';
	echo '</form>';
}

public function add($post)
{
	    $xml = $this->_xml;
		$ids = $xml->xpath("row/id"); // select all ids
		$newid = max(array_map("intval", $ids)) + 1; // change objects to `int`, get `max()`, + 1
        $list = $xml->addChild('row');
		$id = $list->addChild("id", $newid);
		$list->addChild('text', $post['text']);
	    $list->addChild("parent_id",$post['id']);
        $xml->asXML($this->path);
		$this->format_xml_file($this->path);
		$_SESSION['bread_crumbs'] = '';
	    $this->bread_crumbs($id);
		header( 'Location: process.php?display='.$list->parent_id.'' ) ;
}

public function edit($id)
{
	echo $id;
}

public function get_item_by_id($id)
{
    $item = $this->_xml->xpath('row[id="' . $id . '"]');
	return $item[0];
}

public function update($post)
    {
		if ($post["text"] == '')
		{
			echo "no blank text allowed";
		} else {
			
        $list = $this->_xml->xpath('row[id="' . $post['id'] . '"]');
		$list[0]->text = $post["text"];
		$this->getXml()->asXML($this->path);
		$this->format_xml_file($this->path);
		header( 'Location: process.php?display_bread_crumbs='.$list[0]->parent_id.'' ) ;
		}
    }
	
public function delete($post)
	{
        $list = $this->_xml->xpath('list[id="' . $post['id'] . '"]');
		unset($list[0][0]);
		$this->getXml()->asXML($this->path);
		header( 'Location: index.php' ) ;
	}
	
public function format_xml_file($xmlFile)
	{
		if( !file_exists($xmlFile) ) die('Missing file: ' . $xmlFile);
		else
		{
		  $dom = new DOMDocument('1.0');
		  $dom->preserveWhiteSpace = false;
		  $dom->formatOutput = true;
		  $dl = @$dom->load($xmlFile); 
		  if ( !$dl ) die('Error while parsing the document: ' . $xmlFile);
		  $dom->save($xmlFile);
		}
	}

public function bread_crumbs($id)
{
	echo '<a href="process.php?display=0">start</a>';
	echo '&nbsp;';

	//echo '&nspb;';

	$str = $_SESSION['bread_crumbs'];
	if(!$id == '')
	{
		$x = $this->_xml->xpath('row[id="' . $id . '"]');
		$str .= ' >>> <a href="process.php?display_bread_crumbs='.$x[0]->id.'">'.substr($x[0]->text,0,20).'</a>';
		$_SESSION['bread_crumbs'] = $str;
		echo $_SESSION['bread_crumbs'];
		
	}
}
	
	
}//end of class Process

echo '</div></div></div>';


if (session_status() == PHP_SESSION_NONE) {
    session_start();
	//$_SESSION['bread_crumbs'] = '';
}


$path = getcwd()."/data/data.xml";
$process = new Process();
$process->setPath($path);

$param = $_SERVER['QUERY_STRING'];
$arr = explode("=", $param);
if (count($arr) > 1) {
    $param = $arr[0];
	$value = $arr[1];
}
 
if ($param == "display") {
    $process->display($value);
}

if ($param == "more") {
    $process->more($value);
}


if ($param == "add") {
    $post = $_POST;
   	$process->add($post);
}

if ($param == "edit") {
    $item = $process->get_item_by_id($value);
	include 'edit.php';
}

if ($param == "update") {
	$post = $_POST;
    $process->update($post);
}

if ($param == "display_bread_crumbs") {
	$_SESSION['bread_crumbs'] = '';
	$process->display($value);
}
include('footer.php');





