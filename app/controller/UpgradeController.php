<?php
namespace app\controller;

use app\common\BaseController;
use app\service\UpgradeService;
use app\service\PluginLifecycleService;
use support\Request;
use support\Response;

/**
 * 系统升级控制器
 */
class UpgradeController extends BaseController
{
    /**
     * @var UpgradeService
     */
    protected $upgradeService;
    protected $lifecycleService;

    public function __construct()
    {
        $projectCode = env('PROJECT_CODE', 'isvapi');
        $this->upgradeService = new UpgradeService($projectCode);
        $this->lifecycleService = new PluginLifecycleService();
        parent::__construct();
    }

    /**
     * 获取所有补丁包列表
     * @param Request $request
     * @return Response
     */
    public function getPatchList(Request $request): Response
    {
        try {
            $params = $request->get();
            $result = $this->upgradeService->getPatchList($params);
            return $this->success($result);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * 获取指定版本的补丁信息
     * @param Request $request
     * @return Response
     */
    public function getPatchInfo(Request $request): Response
    {
        try {
            $projectCode = $request->get('project_code');
            $version = $request->get('version');
            $result = $this->upgradeService->getPatchInfo($version, $projectCode, $request->get('api_key'), $request->get('api_secret'));
            unset($result['package_url'], $result['md5']);
            return $this->success($result);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * 获取指定版本的下一个补丁信息
     * @param Request $request
     * @return Response
     */
    public function getNextPatchInfo(Request $request): Response
    {
        try {
            $projectCode = $request->get('project_code');
            $version = $request->get('version');
            $result = $this->upgradeService->getNextPatchInfo($version, $projectCode, $request->get('api_key'), $request->get('api_secret'));
            unset($result['package_url'], $result['md5']);
            return $this->success($result);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * 执行升级
     * @param Request $request
     * @return Response
     */
    public function executeUpgrade(Request $request): Response
    {
        try {
            $version = $request->post('version');
            $projectCode = $request->post('project_code');
            if (empty($version) || empty($projectCode)) {
                return $this->fail('版本号或项目代码不能为空');
            }
            
            $result = $this->upgradeService->executeUpgrade($version, $projectCode, $request->post('api_key'), $request->post('api_secret'));
            return $this->success($result);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * 获取升级状态
     * @return Response
     */
    public function getUpgradeStatus(): Response
    {
        try {
            $result = $this->upgradeService->getUpgradeStatus();
            return $this->success($result);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * 获取所有项目及其安装版本（合并接口）
     * @return Response
     */
    public function getAllProjectsWithVersions(): Response
    {
        try {
            $result = $this->upgradeService->getAllProjectsWithVersions();
            return $this->success($result);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    public function previewUninstall(Request $request): Response
    {
        try {
            $plugin = (string)$request->get('plugin_code');
            return $this->success($this->lifecycleService->preview($plugin));
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    public function uninstallPlugin(Request $request): Response
    {
        try {
            $plugin = (string)$request->post('plugin_code');
            $confirmation = (string)$request->post('confirmation');
            return $this->success($this->lifecycleService->uninstall($plugin, $confirmation));
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    public function reinstallPlugin(Request $request): Response
    {
        try {
            $plugin = (string)$request->post('plugin_code');
            $version = (string)$request->post('version');
            $confirmation = (string)$request->post('confirmation');
            return $this->success($this->lifecycleService->reinstall($plugin, $version, $confirmation, $request->post('api_key'), $request->post('api_secret')));
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }
}
