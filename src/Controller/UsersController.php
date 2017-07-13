<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{

    /**
     * Login method
     *
     * @return \Cake\Network\Response|null
     */
    public function login()
    {
        // die('bbb');
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                $this->Auth->setUser($user);
                if ($this->Auth->user('first_login') == 0) {
                    return $this->redirect(['controller' => 'users', 'action'=>'changepassword']);
                } elseif ($this->Auth->user('role') === 'admin') {
                    return $this->redirect(['prefix'=>'admin','controller' => 'users', 'action' => 'index']);
                } else {
                    return $this->redirect($this->Auth->redirectUrl());
                }
            }
            $this->Flash->error(__('Invalid username or password, please try again!'));
        }
    }
    /**
     * Logout method
     *
     * @return \Cake\Network\Response|null
     */
    public function logout()
    {
        return $this->redirect($this->Auth->logout());
    }
    /**
     * dashboard method
     *
     * @return \Cake\Network\Response|null
     */
     public function dashboard()
     {
     }

     /**
      * Change Password method
      *
      * @return \Cake\Network\Response|null
      */
    public function changePassword()
    {
        $user =$this->Users->get($this->Auth->user('id'));
        if ($this->request->data) {
            $user = $this->Users->patchEntity($user, [
              'old_password' => $this->request->data['old_password'],
              'password' => $this->request->data['password1'],
              'password1' => $this->request->data['password1'],
              'password2' => $this->request->data['password2'],
              'first_login' => 'true'
            ],
            ['validate' => 'password']
            );
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The password is successfully changed'));

                if ($user['role'] === 'admin') {
                    $this->redirect('/admin/users/index');
                } else {
                    $this->redirect('/users/index');
                }
            } else {
                $this->Flash->error(__('There was an error during the save!'));
            }
        }
        $this->set('user', $user);
    }

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        // debug($this->request->param('pass'));
        // die;
        $user =$this->Users->get($this->Auth->user('id'));
        $this->set('user', $user);
        $this->set('_serialize', ['user']);
    }
}
