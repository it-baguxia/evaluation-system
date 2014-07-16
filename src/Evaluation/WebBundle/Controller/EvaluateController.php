<?php

namespace Evaluation\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;

//加载用到的实体
use Evaluation\CommonBundle\Entity\Evaluation;
use Evaluation\CommonBundle\Entity\EvaluatedPersonResult;
use Evaluation\CommonBundle\Entity\EvaluatedPerson;

class EvaluateController extends Controller
{
	
	
	public function joinAction(){
		
		
		$builder = $this->createFormBuilder();
		$builder->add('name', 'text')
				->add('description','textarea')
				->add('list','collection');
						

		$subBuilder = $this->createFormBuilder();
		$subform1 = $subBuilder->add('age', 'text')
						      ->add('score','text');
						     
		
		
		$subform2 = $subBuilder->add('age', 'text')
							  ->add('score','text');
							  
							  
		
		$builder->get('list')->add($subform1);
		$builder->get('list')->add($subform2);
		
		$form = $builder->getForm();
		
		
		$formView = $form->createView();
		
		
		$this->getUser();
		
		return $this->render('EvaluationWebBundle:Evaluate:join.html.twig',array('formView'=>$formView));
		
	}
	
	
	
	
	
	public function join1Action()
    {
    	//第一步：验证当前用户是否具有权限
    	
    	
		$doctine = $this->getDoctrine();
		$em = $doctine->getManager();
    	//第二步：验证当前评价是否处于进行中，如果是未开始或者已结束的状态就显示相应的提示
    	
    	//第三步：验证当前用户是否已经提交过数据，如果已经提交过数据，就显示相应的提示
    	
    	//第四步：根据当前用户的信息查询民主评价的相关信息，然后形成相关的表单
    	 //1.根据用户名查询所属的教学评价的ID
		 $username = $this->getUser()->getUsername();//得到用户信息账号
    	 $evaluateUserRepository = $em->getRepository('EvaluationCommonBundle:EvaluateUser');
    	 $evaluateUser = $evaluateUserRepository->findOneByUsername($username);
    	 
    	 if(!$evaluateUser){
    	 	return new Response('没有查询到关于您的账号所关联的民主评价');
    	 }
    	 
    	 //2.查询得到民主评价的相关信息
    	 $evaluationId = $evaluateUser->getEvaluationId();
    	 $evaluationRepository = $em->getRepository('EvaluationCommonBundle:Evaluation');
    	 $evaluation = $evaluationRepository->find($evaluationId);
    	 
    	 if(!$evaluation){
    	 	return new Response('没有查询到关于您的账号所关联的民主评价的详细信息');
    	 }
    	 
    	 
    	 //3.根据民主评价的信息得到测评对象的信息
    	 $evaluatePerson = $evaluation->getEvaluatedPerson();//得到序列化字段
    	 $evaluatePersonIdList = unserialize($evaluatePerson);//得到序列化字段
    	 
    	 $evaluation = new Evaluation();
    	 
    	 foreach($evaluatePersonIdList as $personId){
    	 	
    	 	//
    	 	
    	 	
    	 	$personResult = new EvaluatedPersonResult();
    	 	$personResult->setScore(rand(1,9));
    	 	
    	 	$evaluation->getPerson()->set($personId,$personResult);
    	 }
    	 
    	 
    	 
    	 //2.通过createForm的第三个参数传递options选项，动态的添加表单元素
    	 $form = $this->createForm('evaluate_join_form',$evaluation);
    	 $formView = $form->createView();
    	
    	
    	
         return $this->render('EvaluationWebBundle:Evaluate:join.html.twig',array('formView'=>$formView));
    }
}
