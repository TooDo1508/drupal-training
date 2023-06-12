<?php
namespace Drupal\pfizer_question\Controller;

use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Logger\LoggerChannelTrait;
use Drupal\Core\Messenger\MessengerTrait;
use Drupal\Core\Routing\RedirectDestinationTrait;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\TypedData\TraversableTypedDataInterface;
use Drupal\printable\Controller\PrintableController;
use Drupal\printable\PrintableFormatPluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Drupal\examples\Utility\DescriptionTemplateTrait;
use Mpdf\Mpdf;

/**
 * Provides route responses for the Example module.
 */
class PfizerQuestionController extends ControllerBase {

 /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */

    /**
     * The printable format plugin manager.
     *
     * @var \Drupal\printable\PrintableFormatPluginManager
     */
    protected $printableFormatManager;

    /**
     * The configuration factory.
     *
     * @var \Drupal\Core\Config\ConfigFactory
     */
    protected $configFactory;
    /**
     * Constructs a \Drupal\printable\Controller\PrintableController object.
     *
     * @param \Drupal\printable\PrintableFormatPluginManager $printable_format_manager
     *
     *   The printable format plugin manager.
     */

    public function __construct(PrintableFormatPluginManager $printable_format_manager) {
        $this->printableFormatManager = $printable_format_manager;
    }

    public static function create(ContainerInterface $container) {
        return new static(
            $container->get('printable.format_plugin_manager'),

        );
    }

     public function myPage() {

         $tempstore = \Drupal::service('tempstore.private');
         // Get the store collection.
         $store = $tempstore->get('form_pfizer_question');
         // Get the key/value pair.
         $value = $store->get('page_values');

         $items[]=$value;
      $modal_form = \Drupal::formBuilder()->getForm('\Drupal\pfizer_question\Form\TopicsForm');

      return [

              'content' => [
                  '#theme' => 'summary_question_td',
                  '#cache' => ['max-age' => 0,],
                  '#items' => $items,
              ],
              'form' => $modal_form,
          ];
  }

    public function toPdf(){
        $page_build = $this->myPage();
        $format = $this->printableFormatManager;
        $pdf = $format->createInstance('pdf');
        $pdf->setContent($page_build);

        return $pdf->getResponse();
    }


}