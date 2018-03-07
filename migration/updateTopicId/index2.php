<?php
 
require_once "../../bootstrap-doctrine.php";

require_once '../../app/models/Subject.php';
require_once '../../app/models/Topic.php';
require_once '../../app/models/Question.php';
require_once 'mapping1.php';

$topics = $entityManager->getRepository('Topic')->findAll();

$flag = false;

$newMap = array();
foreach($map as $key => $value){
	if(!array_key_exists($value, $newMap))
		$newMap[$value] = array();
	array_push($newMap[$value], $key);
}
die('finish');

foreach($newMap as $key => $value){
	$topic = $entityManager->getRepository('Topic')->findOneBy(array('id' => $value[0]));
	$subject = $entityManager->getRepository('Subject')->findOneBy(array('id' => $topic->getSubjectId()));
	$newTopic = new Topic($key, $topic->getName(), $topic->getInfo(), $subject);
	$entityManager->persist($newTopic);
}
$entityManager->flush();

foreach($topics as $key => $topic){
	if(!array_key_exists($topic->getId(), $map))
		continue;
	
	echo '---------------'."\r\n";
	echo 'topic name\t'.$topic->getName()."\r\n";
	echo 'topic id\t'.$topic->getId()."\r\n";
	echo '---------------'."\r\n";
	
	$questions = $entityManager->createQueryBuilder()
	->select('q')
	->from('Question', 'q')
	->where('q.topic = :topicId')
	->setParameter('topicId', $topic->getId())
	->getQuery()
	->getResult();
	
	foreach($questions as $key1 => $question){
		$question->setTopicId($newTopic);
		$entityManager->persist($question);
	}
}

foreach($topics as $key => $topic){
	if(!array_key_exists($topic->getId(), $map))
		continue;
	
	echo '---------------'."\r\n";
	echo 'topic name\t'.$topic->getName()."\r\n";
	echo 'topic id\t'.$topic->getId()."\r\n";
	echo '---------------'."\r\n";
	
	$questions = $entityManager->createQueryBuilder()
		->select('q')
		->from('Question', 'q')
		->where('q.topic = :topicId')
		->setParameter('topicId', $topic->getId())
		->getQuery()
		->getResult();
		
	$subject = $entityManager->getRepository('Subject')->findOneBy(array('id' => $topic->getSubjectId()));
	
	$newTopic = $entityManager->getRepository('Topic')->findOneBy(array('id' => $map[$topic->getId()]));
	
	if(!$newTopic){
		$newTopic = new Topic($map[$topic->getId()], $topic->getName(), $topic->getInfo(), $subject);
	}
	
	$entityManager->persist($newTopic);
	
	foreach($questions as $key1 => $question){
		$question->setTopicId($newTopic);
		$entityManager->persist($question);
	}
	
	$entityManager->remove($topic);
	
	$entityManager->flush();
	
}

$entityManager->flush();
echo "done";

?>