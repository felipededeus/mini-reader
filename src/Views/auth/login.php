<div style="max-width: 400px; margin: 0 auto; text-align: center;">
    
    <h2 style="color: #555; font-weight: normal;">Quem é você?</h2>
    
    <?php if (isset($_SESSION['erro_login'])): ?>
        <div style="background: #ffcdd2; color: #c62828; padding: 10px; border-radius: 8px; margin-bottom: 15px; border: 1px solid #ef9a9a;">
            <?php 
                echo $_SESSION['erro_login']; 
                unset($_SESSION['erro_login']); // Limpa o erro após mostrar
            ?>
        </div>
    <?php endif; ?>

    <form action="<?php echo BASE_URL; ?>login/auth" method="POST">
        
        <div style="margin-bottom: 15px; text-align: left;">
            <label style="display: block; color: #888; margin-bottom: 5px; font-weight: bold;">Seu E-mail</label>
            <input type="email" name="email" required 
                   style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 10px; font-size: 16px; outline: none; transition: border 0.3s;"
                   onfocus="this.style.borderColor='#ffb74d'" 
                   onblur="this.style.borderColor='#ddd'"
                   placeholder="ex: joao@escola.com">
        </div>

        <div style="margin-bottom: 20px; text-align: left;">
            <label style="display: block; color: #888; margin-bottom: 5px; font-weight: bold;">Sua Senha Secreta</label>
            <input type="password" name="senha" required 
                   style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 10px; font-size: 16px; outline: none;"
                   onfocus="this.style.borderColor='#ffb74d'" 
                   onblur="this.style.borderColor='#ddd'"
                   placeholder="••••••••">
        </div>

        <div style="margin-bottom: 20px; text-align: left;">
            <label style="color: #666; cursor: pointer; display: flex; align-items: center;">
                <input type="checkbox" name="manter_conectado" style="transform: scale(1.5); margin-right: 10px;"> 
                Lembrar de mim
            </label>
        </div>

        <button type="submit" class="btn-glossy" style="width: 100%; font-size: 18px;">
            Entrar Agora
        </button>

    </form>
    
    <br>
    <a href="<?php echo BASE_URL; ?>home" style="color: #999; text-decoration: none; font-size: 14px;">← Voltar para o início</a>

</div>