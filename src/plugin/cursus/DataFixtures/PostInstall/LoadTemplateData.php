<?php

/*
 * This file is part of the Claroline Connect package.
 *
 * (c) Claroline Consortium <consortium@claroline.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Claroline\CursusBundle\DataFixtures\PostInstall;

use Claroline\CoreBundle\Entity\Template\Template;
use Claroline\CoreBundle\Entity\Template\TemplateType;
use Claroline\CoreBundle\Library\Configuration\PlatformConfigurationHandler;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadTemplateData extends AbstractFixture implements ContainerAwareInterface
{
    private $om;
    private $translator;
    private $config;
    private $templateTypeRepo;
    private $templateRepo;
    private $availableLocales;

    public function setContainer(ContainerInterface $container = null)
    {
        if (!$container) {
            throw new \LogicException('Expected a service container, got null.');
        }

        $this->translator = $container->get('translator');
        $this->config = $container->get(PlatformConfigurationHandler::class);
    }

    public function load(ObjectManager $om)
    {
        $this->om = $om;
        $this->templateTypeRepo = $om->getRepository(TemplateType::class);
        $this->templateRepo = $om->getRepository(Template::class);
        $this->availableLocales = $this->config->getParameter('locales.available');

        $this->createCourseTemplates();
        $this->createSessionTemplates();

        $eventInvitationType = $this->templateTypeRepo->findOneBy(['name' => 'training_event_invitation']);
        $templates = $this->templateRepo->findBy(['name' => 'training_event_invitation']);
        if ($eventInvitationType && empty($templates)) {
            foreach ($this->availableLocales as $locale) {
                $template = new Template();
                $template->setType($eventInvitationType);
                $template->setName('training_event_invitation');
                $template->setLang($locale);
                $template->setTitle($this->translator->trans('training_event_invitation', [], 'template', $locale));
                $content = '%event_name%<br/>';
                $content .= '[%event_start% -> %event_end%]<br/>';
                $content .= '%event_description%<br/><br/>';
                $content .= '%event_location_address%<br/>';
                $template->setContent($content);
                $om->persist($template);
            }
            $eventInvitationType->setDefaultTemplate('training_event_invitation');
            $om->persist($eventInvitationType);
        }

        $eventPresenceType = $this->templateTypeRepo->findOneBy(['name' => 'training_event_presences']);
        $templates = $this->templateRepo->findBy(['name' => 'training_event_presences']);
        if ($eventPresenceType && empty($templates)) {
            foreach ($this->availableLocales as $locale) {
                $template = new Template();
                $template->setType($eventPresenceType);
                $template->setName('training_event_presences');
                $template->setLang($locale);
                $template->setTitle($this->translator->trans('training_event_presences', [], 'template', $locale));
                $content = '%event_name%<br/>';
                $content .= '[%event_start% -> %event_end%]<br/>';
                $content .= '%event_description%<br/><br/>';
                $content .= '%event_presences_table%<br/>';
                $template->setContent($content);
                $om->persist($template);
            }
            $eventPresenceType->setDefaultTemplate('training_event_presences');
            $om->persist($eventPresenceType);
        }

        $om->flush();
    }

    private function createCourseTemplates()
    {
        /** @var TemplateType $templateType */
        $templateType = $this->templateTypeRepo->findOneBy(['name' => 'training_course']);
        $templates = $this->templateRepo->findBy(['name' => 'training_course']);

        if ($templateType && empty($templates)) {
            foreach ($this->availableLocales as $locale) {
                $template = new Template();
                $template->setType($templateType);
                $template->setName('training_course');
                $template->setLang($locale);
                $template->setTitle($this->translator->trans('training_course', [], 'template', $locale));

                $content = "
                    %course_poster%
                    <h1>%course_name% <small>%course_code%</small></h1>
                    
                    <h2>{$this->translator->trans('description', [], 'platform')}</h2>
                    <p>%course_description%</p>
                    <h2>{$this->translator->trans('information', [], 'platform')}</h2>
                    <ul>
                        <li><b>{$this->translator->trans('public_registration', [], 'platform')} : </b> %course_public_registration%</li>
                        <li><b>{$this->translator->trans('duration', [], 'platform')} : </b> %course_default_duration%</li>
                        <li><b>{$this->translator->trans('max_participants', [], 'cursus')} : </b> %course_max_users%</li>
                    </ul>
                ";
                $template->setContent($content);

                $this->om->persist($template);
            }

            $templateType->setDefaultTemplate('training_course');
            $this->om->persist($templateType);
        }
    }

    private function createSessionTemplates()
    {
        /** @var TemplateType $templateType */
        $templateType = $this->templateTypeRepo->findOneBy(['name' => 'training_session']);
        $templates = $this->templateRepo->findBy(['name' => 'training_session']);

        if ($templateType && empty($templates)) {
            foreach ($this->availableLocales as $locale) {
                $template = new Template();
                $template->setType($templateType);
                $template->setName('training_session');
                $template->setLang($locale);
                $template->setTitle($this->translator->trans('training_session', [], 'template', $locale));

                $content = "
                    %session_poster%
                    <h1>%session_name% <small>%session_code%</small></h1>
                    
                    <h2>{$this->translator->trans('description', [], 'platform')}</h2>
                    <p>%session_description%</p>
                    <h2>{$this->translator->trans('information', [], 'platform')}</h2>
                    <ul>
                        <li><b>{$this->translator->trans('access_dates', [], 'platform')} : </b> {$this->translator->trans('date_range', ['start' => '%session_start%', 'end' => '%session_end%'], 'platform')}</li>
                        <li><b>{$this->translator->trans('public_registration', [], 'platform')} : </b> %session_public_registration%</li>
                        <li><b>{$this->translator->trans('duration', [], 'platform')} : </b> %session_default_duration%</li>
                        <li><b>{$this->translator->trans('max_participants', [], 'cursus')} : </b> %session_max_users%</li>
                    </ul>
                ";
                $template->setContent($content);

                $this->om->persist($template);
            }

            $templateType->setDefaultTemplate('training_session');
            $this->om->persist($templateType);
        }

        $sessionInvitationType = $this->templateTypeRepo->findOneBy(['name' => 'training_session_invitation']);
        $templates = $this->templateRepo->findBy(['name' => 'training_session_invitation']);
        if ($sessionInvitationType && empty($templates)) {
            foreach ($this->availableLocales as $locale) {
                $template = new Template();
                $template->setType($sessionInvitationType);
                $template->setName('training_session_invitation');
                $template->setLang($locale);
                $template->setTitle($this->translator->trans('training_session_invitation', [], 'template', $locale));
                $content = '%session_name%<br/>';
                $content .= '[%session_start% -> %session_end%]<br/>';
                $content .= '%session_description%';
                $template->setContent($content);
                $this->om->persist($template);
            }
            $sessionInvitationType->setDefaultTemplate('training_session_invitation');
            $this->om->persist($sessionInvitationType);
        }

        $sessionInvitationType = $this->templateTypeRepo->findOneBy(['name' => 'training_session_confirmation']);
        $templates = $this->templateRepo->findBy(['name' => 'training_session_confirmation']);
        if ($sessionInvitationType && empty($templates)) {
            foreach ($this->availableLocales as $locale) {
                $template = new Template();
                $template->setType($sessionInvitationType);
                $template->setName('training_session_confirmation');
                $template->setLang($locale);
                $template->setTitle($this->translator->trans('training_session_confirmation', [], 'template', $locale));
                $content = '%session_name%<br/>';
                $content .= '[%session_start% -> %session_end%]<br/>';
                $content .= '%session_description%<br/><br/>';
                $content .= '<a href="%registration_confirmation_url%">'.$this->translator->trans('confirm_registration', [], 'actions').'</a>';
                $template->setContent($content);
                $this->om->persist($template);
            }
            $sessionInvitationType->setDefaultTemplate('training_session_confirmation');
            $this->om->persist($sessionInvitationType);
        }
    }
}
