<?php
namespace CanalTP\MttBundle\Form\Type\Block;

use Symfony\Component\Form\FormBuilderInterface;
use CanalTP\MttBundle\Form\Type\BlockType;

class TextType extends BlockType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text')
            ->add('content', 'textarea', array('attr' => array('rows' => 5)))
        ;
        parent::buildForm($builder, $options);
    }

    public function getName()
    {
        return 'text_block';
    }
}
