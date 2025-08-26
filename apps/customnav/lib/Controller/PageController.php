<?php

namespace OCA\CustomNav\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\RedirectResponse;
use OCP\IRequest;
use OCP\IURLGenerator;

class PageController extends Controller {
    
    /** @var IURLGenerator */
    private $urlGenerator;

    public function __construct($appName, IRequest $request, IURLGenerator $urlGenerator) {
        parent::__construct($appName, $request);
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function project() {
        // Replace this URL with your actual project page URL
        $projectUrl = 'https://your-project-url.com';
        return new RedirectResponse($projectUrl);
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function task() {
        // Replace this URL with your actual task page URL  
        $taskUrl = 'https://your-task-url.com';
        return new RedirectResponse($taskUrl);
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function mom() {
        // Replace this URL with your actual MOM (Minutes of Meeting) page URL
        $momUrl = 'https://your-mom-url.com';
        return new RedirectResponse($momUrl);
    }
}
