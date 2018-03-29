<?php

function checkPackageName(array $packages):void
{

    foreach ($packages as $key => $value) {
        if($key !== $value['name']){
            throw new Exception('Error name '.$key.' not equal package '.$value['name']);
        }
  }	

}

function checkDependenciesKey(array $packages):void
{

    foreach ($packages as $key => $value){
        if (!array_key_exists('dependencies', $value)){
            throw new Exception('Error '.$key.' have not field dependencies');
        }
    }

}

function checkDependenciesDescription(array $packages):void
{
    $keys = array_keys($packages);

    foreach ($packages as $key => $value) {
        $correctDependencies = array(array_diff($value['dependencies'], $keys));
        if (!empty($correctDependencies[0])){
            throw new Exception('Error '.$value['name'].' have not corrected dependencies');
        }
    }

}

function checkAcyclicityArray(array $packages,$startNode = 'A'):void
{
    if (empty($packages[$startNode]['dependencies'])){
        return;
    }

    static $visited;
    $visited[] = $startNode;
    $arrayKey= array();
    array_push($arrayKey, $startNode);

    foreach($packages[$startNode]['dependencies'] as $index => $vertex)
    {
        if ((in_array($vertex , $visited)) && (!empty($packages[$startNode]['dependencies']))){
  		    throw new Exception('Error this package have acyclicity');
  	    }
        if( !in_array($vertex , $visited)){
    	    checkAcyclicityArray( $packages , $vertex );
        }
    }
  	
 } 

function ValidatePackageDefinition(array $packages):void
{
	checkAcyclicityArray($packages);
	checkPackageName($packages);
	checkDependenciesKey($packages);
	checkDependenciesDescription($packages);
}

function getAllPackageDependencies(array $packages,string $packageName ):array
{
    static $arrayKey;
    $arrayOut = array();
    $visited[] = $packageName;

    foreach($packages[$packageName]['dependencies'] as $index => $vertex)
    {
        $arrayKey[] = $vertex;
        if( !in_array( $vertex , $visited )){
            getAllPackageDependencies($packages,$vertex);
        }
    }

    if (!empty($arrayKey)) {
        krsort($arrayKey);
        $ResultArray = array_unique($arrayKey);
    } else{
        $ResultArray = $arrayOut;
    }
    return $ResultArray;
}

$packages = [
	'A'=>['name'=>'A',
		'dependencies'=>['B','C']
	],
	'B'=>['name'=>'B',
	'dependencies'=>[]
	],
	'C'=>['name'=>'C',
			'dependencies'=>['B','D']
	],
	'D'=>['name'=>'D',
			'dependencies'=>[]
	],
	];

try 
{
	validatePackageDefinition($packages);
    print_r (getAllPackageDependencies($packages,'A'));
} catch (Exception $e) {
    echo $e->getMessage();
}


?>