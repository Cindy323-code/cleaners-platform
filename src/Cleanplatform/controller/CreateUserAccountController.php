<?php
namespace Controller;

use Entity\User;

class CreateUserAccountController {
    private User $entity;

    public function __construct(string $role) {
        // 使用参数决定用户角色，而不是硬编码，使控制器更灵活
        $this->entity = User::getInstance(['role' => $role]);
    }

    /**
     * 执行创建用户操作
     * @param array $data 用户数据
     * @return bool 操作是否成功
     */
    public function createUser(array $data) : bool {
        // 控制器仅负责数据传输，不处理业务逻辑
        return $this->entity->executeCreate($data);
    }
}
