<?php
namespace Movies\WidgetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Movies\AdminBundle\Entity\Repository\WidgetRepository")
 * @ORM\Table(name="widget")
 */
class Widget
{

    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="title", type="string", length=255)
     */
    protected $title;

    /**
     * @ORM\Column(name="widget_object", type="object")
     */
    protected $widget_object;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Widget
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set widget_object
     *
     * @param \stdClass $widgetObject
     * @return Widget
     */
    public function setWidgetObject($widgetObject)
    {
        $this->widget_object = $widgetObject;
    
        return $this;
    }

    /**
     * Get widget_object
     *
     * @return \stdClass 
     */
    public function getWidgetObject()
    {
        return $this->widget_object;
    }

    public function renderHeaderWidget()
    {
        $str = '<div class="widget">';
        $str .= '<div class="widget-header"><h3>'.$this->title.'</h3></div>';
        return $str;
    }

    public function renderWidget()
    { 
        return $this->renderHeaderWidget().
               '<div class="widget-content"></div>'.
               $this->renderFooterWidget();
    }

    public function renderFooterWidget()
    {
        return '<div class="widget-footer"></div></div>';
    }
}