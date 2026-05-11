<?php
require_once "../processamento/autorizacao.php";
Autorizacao::proteger();
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Principal</title>
    <link rel="stylesheet" href="../css/main.css">
</head>
<body>


<header>
    <div class="menu-btn" id="menu-btn">☰</div>

    <!-- Nome do sistema, fixo ao lado do botão -->
    <span
        style="
            position:absolute;
            left:55px;
            top:50%;
            transform:translateY(-50%);
            color:#ffffff;
            font-size:15px;
            font-weight:700;
            white-space:nowrap;
            user-select:none;
        "
    >
        Nexus Egressos OAB
    </span>

    <h1 id="titulo-pagina">Painel do Sistema</h1>
</header>


    <nav class="sidebar" id="sidebar">
        <ul>
            
        <li class="active" data-titulo="Cadastrar e Importar" onclick="abrirTela('cadastrarImportar.php', this)">
        <img src="../icons/iconcadastrarImportar.png" alt="">
        <span>Cadastrar e Importar</span>
        </li>
       
            <li data-titulo="Relatórios" onclick="abrirTela('relatorios.php', this)">
                <img src="../icons/iconrelatorios.png" alt="">
                <span>Relatórios</span>
            </li>
            <li data-titulo="Relatório de Logs" onclick="abrirTela('logs.php', this)">
                <img src="../icons/iconlogs.png" alt="">
                <span> Relatório de Logs</span>
            </li>
            <li data-titulo="Convites e Comunicados" onclick="abrirTela('convites.php', this)">
                <img src="../icons/iconconvites.png" alt="">
                <span> Convites e Comunicados </span>
            </li>
            <li data-titulo="Gerenciar Administradores" onclick="abrirTela('cadastroAdmin.php', this)">
                <img src="../icons/iconadministradores.png" alt="">
                <span> Gerenciar Administradores </span>

            </li>
            
          <li class="logout-btn" data-titulo="Deslogar" onclick="window.location.href='../processamento/logout.php'">
    <img src="../icons/deslogar.png" alt="">
    <span> Deslogar </span>
</li>





        </ul>
    </nav>

    <main>
        
       <iframe id="conteudo" src="cadastrarImportar.php" frameborder="0"></iframe>


    </main>

<script>
  const sidebar = document.getElementById('sidebar');
  const menuBtn  = document.getElementById('menu-btn');
  const titulo   = document.getElementById('titulo-pagina');
  const iframe   = document.getElementById('conteudo');

  // Mapa de páginas válidas -> título mostrado no header
  const paginas = {
    'cadastrarImportar.php': 'Cadastrar e Importar',
    'relatorios.php':        'Relatórios',
    'logs.php':              'Relatório de Logs',
    'convites.php':          'Convites e Comunicados',
    'cadastroAdmin.php':     'Gerenciar Administradores'
  };

  // Alternar menu lateral
  let menuAberto = true;
  menuBtn.addEventListener('click', () => {
    menuAberto = !menuAberto;
    sidebar.classList.toggle('hidden', !menuAberto);
    document.querySelector('main').classList.toggle('expandido', !menuAberto);
  });

  // Abre página no iframe e destaca item do menu
  function abrirTela(pagina, elemento) {
    if (!paginas[pagina]) return; // evita abrir coisas fora da whitelist
    iframe.src = pagina;
    titulo.textContent = elemento?.dataset?.titulo || paginas[pagina];

    // destaca no menu
    document.querySelectorAll('nav ul li').forEach(li => li.classList.remove('active'));
    if (elemento && elemento.classList) elemento.classList.add('active');

    // atualiza a URL (sem recarregar) para manter ?pagina=...
    history.replaceState(null, '', `?pagina=${encodeURIComponent(pagina)}`);
  }

  // -------- NOVO: ler ?pagina= na carga inicial --------
  (function init() {
    const params = new URLSearchParams(location.search);
    const pg = params.get('pagina');

    // se vier ?pagina=carrega essa; senão mantém padrão
    if (pg && paginas[pg]) {
      // tenta achar o <li> correspondente pelo onclick
      const li = Array.from(document.querySelectorAll('nav ul li'))
        .find(el => (el.getAttribute('onclick') || '').includes(pg));

      abrirTela(pg, li);
    } else {
      // estado inicial padrão
      abrirTela('cadastrarImportar.php', document.querySelector('nav ul li.active'));
    }
  })();
</script>


</body>
</html>
