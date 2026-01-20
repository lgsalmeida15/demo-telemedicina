# Script para criar e configurar novo repositório no GitHub
# Execute após criar o repositório no GitHub

Write-Host "🚀 Configurando novo repositório no GitHub" -ForegroundColor Green
Write-Host ""

# Ler informações do usuário
$githubUser = Read-Host "Digite seu usuário do GitHub (ex: lgsalmeida15)"
$repoName = Read-Host "Digite o nome do repositório (ex: demo-telemedicina)"

# URL do novo repositório
$newRepoUrl = "https://github.com/$githubUser/$repoName.git"

Write-Host ""
Write-Host "📋 Informações do novo repositório:" -ForegroundColor Cyan
Write-Host "   URL: $newRepoUrl"
Write-Host ""

# Confirmar
$confirm = Read-Host "Confirmar mudança? (s/n)"
if ($confirm -ne "s" -and $confirm -ne "S") {
    Write-Host "❌ Operação cancelada" -ForegroundColor Red
    exit
}

# Verificar se está no diretório correto
if (-not (Test-Path ".git")) {
    Write-Host "❌ Erro: Não está em um repositório Git!" -ForegroundColor Red
    Write-Host "Execute este script dentro da pasta demo-telemedicina"
    exit
}

Write-Host ""
Write-Host "1️⃣  Verificando remote atual..." -ForegroundColor Yellow
git remote -v

Write-Host ""
Write-Host "2️⃣  Mudando remote para: $newRepoUrl" -ForegroundColor Yellow
git remote set-url origin $newRepoUrl

Write-Host ""
Write-Host "3️⃣  Verificando mudança..." -ForegroundColor Yellow
git remote -v

Write-Host ""
Write-Host "4️⃣  Verificando status..." -ForegroundColor Yellow
git status --short

# Perguntar se quer adicionar arquivos não commitados
$changes = git status --short
if ($changes) {
    Write-Host ""
    $addFiles = Read-Host "Há arquivos não commitados. Adicionar e commitar? (s/n)"
    if ($addFiles -eq "s" -or $addFiles -eq "S") {
        git add .
        $commitMsg = Read-Host "Digite a mensagem do commit (ou Enter para padrão)"
        if ([string]::IsNullOrWhiteSpace($commitMsg)) {
            $commitMsg = "feat: adicionar configurações Docker completas"
        }
        git commit -m $commitMsg
    }
}

Write-Host ""
Write-Host "5️⃣  Preparando para fazer push..." -ForegroundColor Yellow
Write-Host "   Branch: master" -ForegroundColor Gray
Write-Host "   Remote: origin" -ForegroundColor Gray
Write-Host ""

# Verificar se há commits para push
$commitsAhead = git rev-list --count origin/master..master 2>$null
if ($LASTEXITCODE -ne 0) {
    Write-Host "⚠️  Este será o primeiro push para o novo repositório" -ForegroundColor Yellow
} else {
    Write-Host "📦 Commits prontos para push: $commitsAhead" -ForegroundColor Cyan
}

Write-Host ""
$proceed = Read-Host "Fazer push agora? (s/n)"
if ($proceed -eq "s" -or $proceed -eq "S") {
    Write-Host ""
    Write-Host "6️⃣  Fazendo push para $newRepoUrl" -ForegroundColor Yellow
    Write-Host "   O navegador abrirá automaticamente para autenticação!" -ForegroundColor Cyan
    Write-Host ""
    
    git push -u origin master
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host ""
        Write-Host "✅ Push realizado com sucesso!" -ForegroundColor Green
        Write-Host ""
        Write-Host "🌐 Acesse seu repositório em:" -ForegroundColor Cyan
        Write-Host "   https://github.com/$githubUser/$repoName" -ForegroundColor White
    } else {
        Write-Host ""
        Write-Host "❌ Erro ao fazer push. Verifique as credenciais." -ForegroundColor Red
    }
} else {
    Write-Host ""
    Write-Host "ℹ️  Remote configurado! Execute manualmente:" -ForegroundColor Cyan
    Write-Host "   git push -u origin master" -ForegroundColor White
}

Write-Host ""

