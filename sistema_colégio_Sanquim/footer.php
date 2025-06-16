<style>
    .footer-container {
        margin-top: auto;
        position: relative;
    }

    .onda-rodape {
         position: relative;
        width: 100%;
        margin-top: -1px;
        z-index: 999;
    }

    .onda-rodape svg {
         display: block;
        width: 100%;
        height: 50px;
         fill: var(--color-wave);
    }

    footer {
        background-color: var(--color-footer);
        color: white;
        padding: 1.5rem 0;
        text-align: center;
        width: 100%;
        position: relative;
        z-index: 2;
    }

    .footer p {
        margin: 0;
        padding: 0; 
        color: white;
        line-height: 1.5;
    }
</style>

<div class="footer-container">
    <div class="onda-rodape">
        <svg viewBox="0 0 1440 100" preserveAspectRatio="none" style="transform: scaleX(-1);">
            <path d="M0,0 C480,60 960,40 1440,80 L1440,100 L0,100 Z"></path>
        </svg>
    </div>
    
    <footer>
        <div class="footer">
            <p>Projeto • Colégio Sanquim © <?php echo date('Y'); ?></p>
            <p>Desenvolvido por BitCode</p>
        </div>
    </footer>
</div>