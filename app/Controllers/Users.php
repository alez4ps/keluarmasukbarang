<?php

namespace App\Controllers;

use App\Models\UserModel;

class Users extends BaseController
{
    protected $user;

    public function __construct()
    {
        $this->user = new UserModel();
    }

    public function index()
    {
        $keyword = $this->request->getGet('keyword');

        if ($keyword) {
            $data['users'] = $this->user
                ->like('nama_petugas', $keyword)
                ->orLike('username', $keyword)
                ->orLike('role', $keyword)
                ->orLike('created_at', $keyword)
                ->findAll();
        } else {
            $data['users'] = $this->user->findAll();
        }

        $data['keyword'] = $keyword;

        return view('users/index', $data);
    }

    public function create()
    {
        return view('users/create');
    }

public function store()
{
    $rules = [
        'nama_petugas' => 'required',
        'username'     => 'required|is_unique[users.username]',
        'password'     => 'required|min_length[8]',
        'role'         => 'required',
    ];

    if (! $this->validate($rules)) {
        return redirect()->back()
            ->withInput()
            ->with('errors', $this->validator->getErrors());
    }

    $this->user->insert([
        'nama_petugas' => $this->request->getPost('nama_petugas'),
        'username'     => $this->request->getPost('username'),
        'password'     => password_hash(
            $this->request->getPost('password'),
            PASSWORD_DEFAULT
        ),
        'role'         => $this->request->getPost('role'),
    ]);

    return redirect()->to('/users')->with('success', 'User berhasil ditambahkan');
}

    public function edit($id)
    {
        $data['user'] = $this->user->find($id);
        return view('users/edit', $data);
    }

    public function update($id)
    {
    $rules = [
        'nama_petugas' => 'required',
        'username'     => 'required',
        'role'         => 'required',
    ];

    if ($this->request->getPost('password')) {
        $rules['password'] = 'min_length[8]';
    }

    if (! $this->validate($rules)) {
        return redirect()->back()
            ->withInput()
            ->with('errors', $this->validator->getErrors());
    }

    $data = [
        'nama_petugas' => $this->request->getPost('nama_petugas'),
        'username'     => $this->request->getPost('username'),
        'role'         => $this->request->getPost('role'),
    ];

    if ($this->request->getPost('password')) {
        $data['password'] = password_hash(
            $this->request->getPost('password'),
            PASSWORD_DEFAULT
        );
    }

    $this->user->update($id, $data);

    return redirect()->to('/users')->with('success', 'User berhasil diupdate');
    }


    public function delete($id)
    {
        if ($id == session()->get('id')) {
            return redirect()->to('/users')->with('error', 'Anda tidak bisa menghapus akun sendiri!');
        }

        $this->user->delete($id);

        return redirect()->to('/users')->with('success', 'User berhasil dihapus');
    }

    public function print()
    {
        $data['users'] = $this->user->findAll();
        return view('users/print', $data);
    }
}
