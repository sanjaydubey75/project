<?php
 
require_once "../../bootstrap-doctrine.php";

require_once '../../app/models/Subject.php';
require_once '../../app/models/Topic.php';
require_once '../../app/models/Question.php';
require_once 'mapping.php';

$topics = $entityManager->getRepository('Topic')->findAll();

$flag = false;

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