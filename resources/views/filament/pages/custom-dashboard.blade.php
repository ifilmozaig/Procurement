<x-filament-panels::page class="overflow-visible">
    <div style="display: grid; grid-template-columns: 400px 1fr; gap: 2rem; margin-bottom: 2rem;">
        {{-- Widget di kiri atas --}}
        <div>
            <x-filament-widgets::widgets
                :widgets="$this->getWidgets()"
                :columns="1"
            />
        </div>
        
        {{-- Space kosong di kanan --}}
        <div></div>
    </div>

    {{-- Welcome text di tengah --}}
    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 40vh; margin-top: -4rem;">
        <div style="text-align: center;">
            <h1 style="font-size: 3rem; font-weight: bold; margin-bottom: 1rem; opacity: 0; animation: fadeIn 1s ease-out forwards;">
                <span style="color: light-dark(#374151, #f3f4f6);">Welcome to Procurement system</span>
            </h1>
            <h2 style="font-size: 4rem; font-weight: 900; color: #f97316; opacity: 0; animation: slideUp 1.2s ease-out 0.3s forwards;">
                KONNCO STUDIO
            </h2>
        </div>

        {{-- Red Panda Image dengan Speech Bubble --}}
        <div style="margin-top: 3rem; opacity: 0; animation: pandaAppear 1.5s ease-out 0.6s forwards; position: relative;">
            {{-- Speech Bubble --}}
            <div id="speechBubble" class="speech-bubble">
                <p style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #1f2937;">
                    Ayo Membuat Procurement! 🚀
                </p>
                <div class="speech-arrow"></div>
            </div>

            {{-- Red Panda Image (Clickable) --}}
            <img 
                id="redPanda"
                src="{{ asset('images/panda.png') }}" 
                alt="Red Panda" 
                style="
                    width: 350px; 
                    height: auto; 
                    filter: drop-shadow(0 15px 30px rgba(0,0,0,0.2)); 
                    animation: pandaBounce 2.5s ease-in-out infinite;
                    cursor: pointer;
                    transition: transform 0.2s ease;
                "
                onclick="showSpeech()"
                onmouseover="this.style.transform='scale(1.05)'"
                onmouseout="this.style.transform='scale(1)'"
            />
        </div>
    </div>

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pandaAppear {
            from {
                opacity: 0;
                transform: scale(0.3) translateY(30px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        @keyframes pandaBounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-12px);
            }
        }

        @keyframes bubblePop {
            0% {
                opacity: 0;
                transform: scale(0.3) translateY(20px);
            }
            60% {
                transform: scale(1.1) translateY(-5px);
            }
            100% {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        @keyframes bubbleShake {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            25% { transform: translateY(-3px) rotate(-2deg); }
            75% { transform: translateY(-3px) rotate(2deg); }
        }

        @keyframes bubbleFadeOut {
            0% {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
            100% {
                opacity: 0;
                transform: scale(0.8) translateY(-15px);
            }
        }

        .speech-bubble {
            position: absolute;
            top: -80px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 3px solid #f59e0b;
            border-radius: 20px;
            padding: 15px 25px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            white-space: nowrap;
            z-index: 10;
            opacity: 0;
            pointer-events: none;
        }

        .speech-bubble.show {
            animation: bubblePop 0.5s ease-out forwards, bubbleShake 0.5s ease-in-out 0.5s 2;
        }

        .speech-bubble.hide {
            animation: bubbleFadeOut 0.8s ease-out forwards;
        }

        .speech-arrow {
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 15px solid transparent;
            border-right: 15px solid transparent;
            border-top: 15px solid #f59e0b;
        }

        .speech-arrow::after {
            content: '';
            position: absolute;
            bottom: 3px;
            left: -12px;
            width: 0;
            height: 0;
            border-left: 12px solid transparent;
            border-right: 12px solid transparent;
            border-top: 12px solid #fde68a;
        }

        @media (max-width: 768px) {
            div[style*="grid-template-columns"] {
                grid-template-columns: 1fr !important;
            }
            
            #redPanda {
                width: 180px !important;
            }

            .speech-bubble {
                font-size: 0.9rem !important;
                padding: 12px 20px !important;
                top: -70px;
            }
        }
    </style>

    <script>
        let hideTimeout;

        function showSpeech() {
            const bubble = document.getElementById('speechBubble');
            const panda = document.getElementById('redPanda');
            
            if (hideTimeout) {
                clearTimeout(hideTimeout);
            }
            bubble.classList.remove('show', 'hide');
            void bubble.offsetWidth;
            bubble.classList.add('show');
            panda.style.animation = 'none';
            setTimeout(() => {
                panda.style.animation = 'pandaBounce 0.5s ease-in-out 3, pandaBounce 2.5s ease-in-out 1.5s infinite';
            }, 10);
            hideTimeout = setTimeout(() => {
                bubble.classList.remove('show');
                bubble.classList.add('hide');
                setTimeout(() => {
                    bubble.classList.remove('hide');
                }, 800);
            }, 2500);
        }
    </script>
</x-filament-panels::page>