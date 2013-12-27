<?php
namespace Movies\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Serie\SerieBundle\Entity\Genre;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', 'text', array('attr' => array('class' => 'form-control')))
        		->add('firstname', 'text', array('attr' => array('class' => 'form-control')))
        		->add('lastname', 'text', array('attr' => array('class' => 'form-control')))
        		->add('email', 'email', array('attr' => array('class' => 'form-control')))	
        		->add('password', 'repeated', array(
				    'type' => 'password',
				    'invalid_message' => 'The password fields must match.',
				    'required' => true,
				    'first_options'  => array('label' => 'Password', 'attr' => array('class' => 'form-control')),
				    'second_options' => array('label' => 'Confirm', 'attr' => array('class' => 'form-control'))
				))
				->add('file', 'file', array('image_path' => 'webPath'))
				->add('rolesCollection', 'entity', array('class' => 'MoviesAdminBundle:Role', 'property' => 'name', 'multiple' => true))
				->add('cancel', 'button', array('attr' => array('class' => 'btn btn-cancel col-lg-offset-3')))
				->add('apply', 'submit', array('attr' => array('class' => 'btn btn-apply')))
	            ->add('save', 'submit', array('attr' => array('class' => 'btn btn-save')));


    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'roles' => array()
        ));
    }

    public function getName()
    {
        return 'users';
    }
}