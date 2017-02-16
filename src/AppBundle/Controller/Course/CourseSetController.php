<?php

namespace AppBundle\Controller\Course;

use Biz\Course\Service\CourseService;
use Biz\Course\Service\CourseSetService;
use Biz\Course\Service\CourseNoteService;
use Biz\Task\Service\TaskService;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Common\ArrayToolkit;
use AppBundle\Controller\BaseController;

class CourseSetController extends BaseController
{
    public function showAction(Request $request, $id)
    {
        $course = $this->getCourseService()->getFirstPublishedCourseByCourseSetId($id);
        if (empty($course)) {
            throw $this->createNotFoundException('No Avaliable Course in CourseSet#{$id}');
        }

        return $this->redirect($this->generateUrl('course_show', array('id' => $course['id'])));
    }

    public function courseSetsBlockAction(array $courseSets, $view = 'list', $mode = 'default')
    {
        $userIds = array();

        $service = $this->getCourseService();

        $courseSets = array_map(function ($set) use (&$userIds, $service) {
            $set['course'] = $service->getFirstPublishedCourseByCourseSetId($set['id']);
            if (!empty($set['course']['teacherIds']) && is_array($set['course']['teacherIds'])) {
                $userIds = array_merge($userIds, $set['course']['teacherIds']); 
            }
            return $set;
        }, $courseSets);

        $users = $this->getUserService()->findUsersByIds($userIds);

        return $this->render("course-set/block/course-block-{$view}.html.twig", array(
            'courseSets' => $courseSets,
            'users'      => $users,
            'mode'       => $mode
        ));
    }

    public function favoriteAction($id)
    {
        $success = $this->getCourseSetService()->favorite($id);
        return $this->createJsonResponse($success);
    }

    public function unfavoriteAction($id)
    {
        $success = $this->getCourseSetService()->unfavorite($id);
        return $this->createJsonResponse($success);
    }

    /**
     * @return CourseService
     */
    protected function getCourseService()
    {
        return $this->createService('Course:CourseService');
    }

    /**
     * @return CourseSetService
     */
    protected function getCourseSetService()
    {
        return $this->createService('Course:CourseSetService');
    }
}