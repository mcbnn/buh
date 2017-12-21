<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\FileForm;
use AppBundle\Entity\BuhSchet;
use AppBundle\Entity\BuhDirection;
use AppBundle\Entity\BuhHistory;

use Kily\Tools1C\ClientBankExchange\Parser;

class BuhPayController extends Controller {


    const STATUS_OK           = 'ОК';
    const STATUS_BLOCK        = 'Фильтр блокировки';
    const STATUS_NOT_SCHET    = 'Нет такого счета';
    const STATUS_CHECK_SQULAP = 'Такой платеж уже есть в эскулапе';

    const STATUS_NUM_OK           = 1;
    const STATUS_NUM_BLOCK        = 2;
    const STATUS_NUM_NOT_SCHET    = 3;
    const STATUS_NUM_CHECK_SQULAP = 4;

    const STATUS_COLOR_OK           = 'b_white';
    const STATUS_COLOR_BLOCK        = 'b_block';
    const STATUS_COLOR_NOT_SCHET    = 'b_not_schet';
    const STATUS_COLOR_CHECK_SQULAP = 'b_check_squlap';

    protected $content = NULL;
    protected $schets  = NULL;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em = NULL;

    /**
     * @return array|\Doctrine\ORM\EntityManager|object
     */
    protected function getEntitymanager() {

        if ($this->em === NULL) {
            $this->em = $this->getDoctrine()->getEntityManager();
        }

        return $this->em;
    }

    /**
     * @Route("/add", name="add")
     */
    public function addAction(Request $request) {

        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getEntitymanager();

        $storage = $em->getRepository(BuhSchet::class);

        $form = $this->createForm(FileForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $history = $form->getData();
            $file = $history->getAttachment();
            if ($file) {
                $fileName = md5(uniqid()).'.'.$file->guessExtension();;
                $file->move(
                    $this->getParameter('brochures_directory'),
                    $fileName
                );
                $history->setFile($fileName);
            }

            $history->setIdUser($this->getUser()->getId());
            $em->persist($history);

            $client = new Parser($this->getParameter('brochures_directory').'/'.$fileName);
            $schets = $this->validSchet($client->documents);

            if (!$schets) {
                $this->addFlash(
                    'alert alert-dismissible alert-danger',
                    'Проблема при разборе файла'
                );
            }

            return $this->render(
                'AppBundle:BuhPay:list-schet-parse.html.twig',
                array(
                    'schets' => $schets
                )
            );
        }

        return $this->render(
            'AppBundle:BuhPay:add.html.twig',
            array(
                'form' => $form->createView(),
            )
        );

    }

    /**
     * @Route("/buh-pay/save", name="save")
     */
    public function saveAction(Request $request) {
        $isPOST = $request->isMethod('POST');
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getEntitymanager();
        $storage = $em->getRepository(BuhSchet::class);
        if ($isPOST) {
            $post = $request->request->all();
            $block = $request->request->get('block', null);
            var_Dump(1);
            if (!empty($post['D_OPERATION'])) {
                var_Dump(2);
                $count = 0;
                $double = 0;
                $allTask = 0;
                $notSchet = 0;
                $filter = 0;
                foreach ($post['D_OPERATION'] as $k => $v) {
                    var_Dump(3);
                    $allTask++;
                    $post['change'][ $k ] = "Запись не произведена";
                    $post['change_color'][ $k ] = 'b_block';
                    $status = $post['status'][ $k ];

                    if(!isset($post['download'][$k])) {
                        if (self::STATUS_NUM_NOT_SCHET == $status) $notSchet++;
                        if (self::STATUS_NUM_CHECK_SQULAP == $status) $double++;
                        if (self::STATUS_NUM_BLOCK == $status) $filter++;
                        continue;
                    }

                    $id_direction = $post['id_direction'][ $k ];
                    $storage = $em->getRepository(BuhDirection::class);
                    $buhDirection = $storage->findOneBy([ 'idDirection' => $id_direction ]);
                    $post['short_name'][ $k ] = $buhDirection->getShortName();
                    $connect = $buhDirection->getFirebird();
                    $pu = $this->container->getParameter('sqlup');
                    $db = new \PDO((string)$connect, $pu['user'], $pu['pass']);

                    $data = [];
                    $data['D_OPERATION'] = $v;
                    $data['D_DOC'] = $post['D_DOC'][ $k ];
                    $data['CORESPOND'] = $post['CORESPOND'][ $k ];
                    $data['NUMDOC'] = $post['NUMDOC'][ $k ];
                    $data['CREDIT'] = $post['CREDIT'][ $k ];
                    $data['COMMENT'] = $post['COMMENT'][ $k ];
                    $data['INN_CORESPOND'] = $post['INN_CORESPOND'][ $k ];
                    $data['MD5'] = $post['MD5'][ $k ];
                    var_dump($data);
                    $sql = "INSERT INTO BANK (
													ID_BANK, 
													D_OPERATION,
													D_DOC,
													CORESPOND,
													NUMDOC, 
													CREDIT, 
													COMMENT, 
													INN_CORESPOND, 
													MD5 
													) 
													VALUES (gen_id(GEN_OBJECT,1),
													'" . implode('\',\'', $data) . "'
													)";


                    try {
                        var_dump($sql);
                        $db->query($sql);
                        $post['change'][ $k ] = "Запись произведена";
                        $post['change_color'][ $k ] = 'b_white';
                        $count++;
                    }
                    catch (\Exception $e) {
                        $post['change'][ $k ] = "Запись не произведена при внесение в эскулап";
                        syslog(LOG_ERR, json_encode([
                            'name'       => 'buh_squlap',
                            'getMessage' => $e->getMessage(),
                        ]));

                    }

                }
            }

            return $this->render(
                'AppBundle:BuhPay:save-send.html.twig',
               [
                   'schets'   => $post,
                   'count'    => $count,
                   'double'   => $double,
                   'allTask'  => $allTask,
                   'notSchet' => $notSchet,
                   'filter'   => $filter,
                   'block'    => $block
               ]
            );
        }


    }




    public function validSchet($content) {

        $schet = [];
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getEntitymanager();

        foreach ($content as $k => $d) {


            $getPayerSchet = ($d['data']['ПлательщикСчет']) ? $d['data']['ПлательщикСчет'] : $d['data']['ПлательщикРасчСчет'];
            $getSchet = ($d['data']['ПолучательСчет']) ? $d['data']['ПолучательСчет'] : $d['data']['ПолучательРасчСчет'];
            if ($d['data']['ДатаСписано']) continue;
            $storage = $em->getRepository(BuhSchet::class);
            $buhPayerSchet = $storage->findOneBy([
                'schet' => $getPayerSchet,
                'del'   => NULL
            ]);
            $buhSchet = $storage->findOneBy([
                'schet' => $getSchet,
                'del'   => NULL
            ]);

            if ($buhSchet) {
                if (!isset($schet[ $getSchet ]['schet'])) {
                    $schet[ $getSchet ]['short_name'] = $buhSchet->getIdDirection()->getShortName();
                    $schet[ $getSchet ]['id_direction'] = $buhSchet->getIdDirection()->getIdDirection();
                }
            }
            else {
                $schet[ $getSchet ]['short_name'] = "<span style = 'color:red'>Не выбрано направление</span>";
                $schet[ $getSchet ]['id_direction'] = 0;
            }

            $schet[ $getSchet ]['data'][ $k ]['D_OPERATION'] = $d['data']['ДатаПоступило'];
            $schet[ $getSchet ]['data'][ $k ]['D_DOC'] = $d['data']['Дата'];
            $schet[ $getSchet ]['data'][ $k ]['CORESPOND'] = $d['data']['ПлательщикИНН'] . ' ';
            if (isset($d['data']['Плательщик']) and !empty($d['data']['Плательщик'])) $schet[ $getSchet ]['data'][ $k ]['CORESPOND'] .= $d['data']['Плательщик'];
            if (isset($d['data']['Плательщик1']) and !empty($d['data']['Плательщик1'])) $schet[ $getSchet ]['data'][ $k ]['CORESPOND'] .= $d['data']['Плательщик1'];
            if (isset($d['data']['Плательщик2']) and !empty($d['data']['Плательщик2'])) $schet[ $getSchet ]['data'][ $k ]['CORESPOND'] .= $d['data']['Плательщик2'];
            if (isset($d['data']['Плательщик3']) and !empty($d['data']['Плательщик3'])) $schet[ $getSchet ]['data'][ $k ]['CORESPOND'] .= $d['data']['Плательщик3'];
            if (isset($d['data']['Плательщик4']) and !empty($d['data']['Плательщик4'])) $schet[ $getSchet ]['data'][ $k ]['CORESPOND'] .= $d['data']['Плательщик4'];
            if (isset($d['data']['Плательщик5']) and !empty($d['data']['Плательщик5'])) $schet[ $getSchet ]['data'][ $k ]['CORESPOND'] .= $d['data']['Плательщик5'];
            if (isset($d['data']['Плательщик6']) and !empty($d['data']['Плательщик6'])) $schet[ $getSchet ]['data'][ $k ]['CORESPOND'] .= $d['data']['Плательщик6'];
            $schet[ $getSchet ]['data'][ $k ]['NUMDOC'] = $d['data']['Номер'];

            $schet[ $getSchet ]['data'][ $k ]['COMMENT'] = "";
            $schet[ $getSchet ]['data'][ $k ]['PAYER_SCHET'] = $getPayerSchet;
            $schet[ $getSchet ]['data'][ $k ]['SCHET'] = $buhSchet;
            if (isset($d['data']['НазначениеПлатежа']) and !empty($d['data']['НазначениеПлатежа'])) $schet[ $getSchet ]['data'][ $k ]['COMMENT'] .= $d['data']['НазначениеПлатежа'];
            if (isset($d['data']['НазначениеПлатежа 1']) and !empty($d['data']['НазначениеПлатежа 1'])) $schet[ $getSchet ]['data'][ $k ]['COMMENT'] .= $d['data']['НазначениеПлатежа 1'];
            if (isset($d['data']['НазначениеПлатежа 2']) and !empty($d['data']['НазначениеПлатежа 2'])) $schet[ $getSchet ]['data'][ $k ]['COMMENT'] .= $d['data']['НазначениеПлатежа 2'];
            if (isset($d['data']['НазначениеПлатежа 3']) and !empty($d['data']['НазначениеПлатежа 3'])) $schet[ $getSchet ]['data'][ $k ]['COMMENT'] .= $d['data']['НазначениеПлатежа 3'];
            if (isset($d['data']['НазначениеПлатежа 4']) and !empty($d['data']['НазначениеПлатежа 4'])) $schet[ $getSchet ]['data'][ $k ]['COMMENT'] .= $d['data']['НазначениеПлатежа 4'];
            if (isset($d['data']['НазначениеПлатежа 5']) and !empty($d['data']['НазначениеПлатежа 5'])) $schet[ $getSchet ]['data'][ $k ]['COMMENT'] .= $d['data']['НазначениеПлатежа 5'];
            if (isset($d['data']['НазначениеПлатежа 6']) and !empty($d['data']['НазначениеПлатежа 6'])) $schet[ $getSchet ]['data'][ $k ]['COMMENT'] .= $d['data']['НазначениеПлатежа 6'];
            if (isset($d['data']['НазначениеПлатежа1']) and !empty($d['data']['НазначениеПлатежа1'])) $schet[ $getSchet ]['data'][ $k ]['COMMENT'] .= $d['data']['НазначениеПлатежа1'];
            if (isset($d['data']['НазначениеПлатежа2']) and !empty($d['data']['НазначениеПлатежа2'])) $schet[ $getSchet ]['data'][ $k ]['COMMENT'] .= $d['data']['НазначениеПлатежа2'];
            if (isset($d['data']['НазначениеПлатежа3']) and !empty($d['data']['НазначениеПлатежа3'])) $schet[ $getSchet ]['data'][ $k ]['COMMENT'] .= $d['data']['НазначениеПлатежа3'];
            if (isset($d['data']['НазначениеПлатежа4']) and !empty($d['data']['НазначениеПлатежа4'])) $schet[ $getSchet ]['data'][ $k ]['COMMENT'] .= $d['data']['НазначениеПлатежа4'];
            if (isset($d['data']['НазначениеПлатежа5']) and !empty($d['data']['НазначениеПлатежа5'])) $schet[ $getSchet ]['data'][ $k ]['COMMENT'] .= $d['data']['НазначениеПлатежа5'];
            if (isset($d['data']['НазначениеПлатежа6']) and !empty($d['data']['НазначениеПлатежа6'])) $schet[ $getSchet ]['data'][ $k ]['COMMENT'] .= $d['data']['НазначениеПлатежа6'];

            $schet[ $getSchet ]['data'][ $k ]['INN_CORESPOND'] = $d['data']['ПлательщикИНН'];

            $credit = $d['data']['Сумма'];
            //if(!stristr($credit, '.'))$credit .= '.00';
            $schet[ $getSchet ]['data'][ $k ]['CREDIT'] = $credit;

            if (!stristr($credit, '.')) {
                $credit_2 = $credit . '.00';
            }
            else {
                $credit_2 = str_replace('.00', '', $credit);
            }

            $md5 = md5(implode('', [
                        $schet[ $getSchet ]['data'][ $k ]['NUMDOC'],
                        $credit,
                        $schet[ $getSchet ]['data'][ $k ]['INN_CORESPOND'],
                        $schet[ $getSchet ]['data'][ $k ]['COMMENT']
                    ]));

            $md5_2 = md5(implode('', [
                        $schet[ $getSchet ]['data'][ $k ]['NUMDOC'],
                        $credit_2,
                        $schet[ $getSchet ]['data'][ $k ]['INN_CORESPOND'],
                        $schet[ $getSchet ]['data'][ $k ]['COMMENT']
                    ]));

            $schet[ $getSchet ]['data'][ $k ]['MD5'] = $md5;
            $schet[ $getSchet ]['data'][ $k ]['MD5_2'] = $md5_2;
            $schet[ $getSchet ][ $k ]['data'] = $d['data'];
            $schet[ $getSchet ]['data'][ $k ]['status']['num'] = self::STATUS_NUM_OK;
            $schet[ $getSchet ]['data'][ $k ]['status']['text'] = self::STATUS_OK;
            $schet[ $getSchet ]['data'][ $k ]['status']['color'] = self::STATUS_COLOR_OK;

            if (!$buhSchet) {
                $schet[ $getSchet ]['data'][ $k ]['status']['num'] = self::STATUS_NUM_NOT_SCHET;
                $schet[ $getSchet ]['data'][ $k ]['status']['text'] = self::STATUS_NOT_SCHET;
                $schet[ $getSchet ]['data'][ $k ]['status']['color'] = self::STATUS_COLOR_NOT_SCHET;

            }
            else {
                $connect = $buhSchet->getIdDirection()->getFirebird();
                $pu = $this->container->getParameter('sqlup');
                $db = new \PDO((string)$connect, $pu['user'], $pu['pass']);
                $schet[ $getSchet ]['data'][ $k ]['CREDIT'] = $credit;
                $sql = "Select * from BANK where md5 = '$md5' or md5  = '$md5_2'";
                $st = $db->prepare($sql);
                $st->execute();
                $rl = $st->fetch();
                if ($rl) {
                    $schet[ $getSchet ]['data'][ $k ]['status']['num'] = self::STATUS_NUM_CHECK_SQULAP;
                    $schet[ $getSchet ]['data'][ $k ]['status']['text'] = self::STATUS_CHECK_SQULAP;
                    $schet[ $getSchet ]['data'][ $k ]['status']['color'] = self::STATUS_COLOR_CHECK_SQULAP;
                }

                elseif (!empty($buhPayerSchet)) {
                    $schet[ $getSchet ]['data'][ $k ]['status']['num'] = self::STATUS_NUM_BLOCK;
                    $schet[ $getSchet ]['data'][ $k ]['status']['text'] = self::STATUS_BLOCK;
                    $schet[ $getSchet ]['data'][ $k ]['status']['color'] = self::STATUS_COLOR_BLOCK;
                }
            }
        }

        return $schet;
    }


}