<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class UserGuideController extends Controller
{
  public function index(): Response
  {
    return Inertia::render('UserGuide/Index', [
      'sections' => $this->getSections(),
      'tableOfContents' => $this->getTableOfContents(),
    ]);
  }

  public function show(string $section): Response
  {
    $content = $this->getGuideContent($section);

    if (!$content) {
      abort(404);
    }

    return Inertia::render('UserGuide/Section', [
      'section' => $section,
      'content' => $content,
      'navigation' => $this->getNavigation($section),
    ]);
  }

  private function getSections(): array
  {
    return [
      'getting-started' => [
        'title' => 'Getting Started',
        'icon' => 'play',
        'subsections' => [
          'overview' => 'System Overview',
          'login' => 'Logging In',
          'navigation' => 'Navigating the System',
        ],
      ],
      'requisitioner' => [
        'title' => 'Requisitioner Guide',
        'icon' => 'document',
        'subsections' => [
          'submit' => 'Submitting Concept Papers',
          'track' => 'Tracking Submissions',
          'attachments' => 'Managing Attachments',
        ],
      ],
      'approver' => [
        'title' => 'Approver Guide',
        'icon' => 'check-circle',
        'subsections' => [
          'review' => 'Reviewing Papers',
          'complete' => 'Completing Stages',
          'return' => 'Returning Papers',
          'deadlines' => 'Managing Deadlines',
        ],
      ],
      'admin' => [
        'title' => 'Administrator Guide',
        'icon' => 'cog',
        'subsections' => [
          'access' => 'Accessing Admin Dashboard',
          'users' => 'Managing Users',
          'reports' => 'Generating Reports',
          'troubleshooting' => 'Troubleshooting',
        ],
      ],
      'workflow' => [
        'title' => 'Workflow Process',
        'icon' => 'flow',
        'subsections' => [
          'stages' => 'Understanding Stages',
          'roles' => 'Role Responsibilities',
          'timeline' => 'Processing Timeline',
        ],
      ],
      'faq' => [
        'title' => 'FAQ',
        'icon' => 'question',
        'subsections' => [
          'account' => 'Account Questions',
          'files' => 'File Upload Questions',
          'notifications' => 'Notification Questions',
          'support' => 'Getting Support',
        ],
      ],
    ];
  }

  private function getGuideContent(string $section): ?array
  {
    // Load markdown content from storage
    $filePath = resource_path("docs/user-guide/{$section}.md");

    if (!file_exists($filePath)) {
      return null;
    }

    return [
      'markdown' => file_get_contents($filePath),
      'title' => $this->getSectionTitle($section),
      'lastUpdated' => date('F j, Y', filemtime($filePath)),
    ];
  }

  private function getTableOfContents(): array
  {
    $sections = $this->getSections();
    $toc = [];

    foreach ($sections as $key => $section) {
      $toc[] = [
        'id' => $key,
        'title' => $section['title'],
        'icon' => $section['icon'],
        'subsections' => $section['subsections'],
      ];
    }

    return $toc;
  }

  private function getNavigation(string $currentSection): array
  {
    $sections = array_keys($this->getSections());
    $currentIndex = array_search($currentSection, $sections);

    return [
      'previous' => $currentIndex > 0 ? $sections[$currentIndex - 1] : null,
      'next' => $currentIndex < count($sections) - 1 ? $sections[$currentIndex + 1] : null,
    ];
  }

  private function getSectionTitle(string $section): string
  {
    $sections = $this->getSections();
    return $sections[$section]['title'] ?? 'User Guide';
  }
}
