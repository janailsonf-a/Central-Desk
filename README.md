# 🚀 CentralDesk API

![Laravel](https://img.shields.io/badge/Laravel-10-red)
![PHP](https://img.shields.io/badge/PHP-8.2-blue)
![Status](https://img.shields.io/badge/status-em%20desenvolvimento-yellow)
![License](https://img.shields.io/badge/license-MIT-green)

Sistema de Help Desk desenvolvido com Laravel, focado em gestão de chamados (tickets), SLA, histórico e organização por empresas.

---

## 📌 Sobre o projeto

O CentralDesk é uma API RESTful que simula um sistema real de suporte técnico.

O sistema permite gerenciar chamados do início ao fim, com controle de SLA, histórico completo e anexos.

Este projeto foi desenvolvido com foco em:

- boas práticas de backend
- organização em camadas (Services)
- escalabilidade
- código limpo

---

## ⚙️ Funcionalidades

### 🔐 Autenticação
- Login com Laravel Sanctum
- Proteção de rotas
- Usuário autenticado

### 👥 Usuários
- Cadastro de usuários
- Multiempresa
- Perfis: admin, técnico, solicitante

### 🏢 Estrutura
- Empresas
- Departamentos
- Categorias

### 🎫 Tickets
- Criar chamado
- Atribuir técnico
- Alterar status
- Listar e filtrar

### 🧠 SLA
- SLA por prioridade
- Cálculo automático de prazo due_at)
- Controle de atraso is_overdue)

### 📝 Comentários
- Comentários internos e externos

### 📎 Anexos
- Upload de arquivos
- Associação com tickets

### 📜 Histórico
- Registro automático de ações

### 📊 Dashboard
- Métricas por status
- Métricas por prioridade
- Chamados recentes

### ⚙️ Jobs e filas
- Processamento assíncrono
- Base pronta para envio de e-mails

---

## 🛠️ Tecnologias

- PHP 8+
- Laravel 10+
- MySQL / SQLite
- Laravel Sanctum
- Queue (Jobs)
- Mail

---

## 📦 Instalação

```bash
git clone https://github.com/SEU-USUARIO/centraldesk.git
cd centraldesk

composer install

cp .env.example .env

php artisan key:generate