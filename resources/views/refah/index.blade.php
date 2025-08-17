
    @include('refah.partials.header')

    <!-- Main Content -->
    <main class="main-content">
        <!-- Introduction Section -->
        <section class="intro-section">
            <div class="container">
                <h1 class="intro-title">خوش آمدید به سامانه رفاه کالا</h1>
                <p class="intro-text">
                    سامانه رفاه کالا با هدف ارائه بهترین خدمات رفاهی و تامین نیازهای روزمره خانواده‌های محترم طراحی شده است.
                    ما با ارائه بسته‌های متنوع و با کیفیت، در نظر داریم تا زندگی شما را آسان‌تر و با کیفیت‌تر کنیم.
                    از طریق این سامانه می‌توانید به راحتی ثبت‌نام کرده و از خدمات ویژه ما بهره‌مند شوید.
                </p>
            </div>
        </section>

        <!-- Registration Box -->
        <section class="container">
            <div class="registration-box" onclick="window.location.href='{{ route('refah.registration.form') }}'">
                <img src="{{ asset('images/slide1.jpeg') }}" alt="ثبت نام در سامانه رفاه کالا" onerror="this.style.display='none'">
                <h2 class="registration-box-title">فرم ثبت نام رفاه کالا آنلاین</h2>
                <p class="registration-box-subtitle">برای استفاده از خدمات رفاه کالا، لطفاً ثبت نام کنید</p>
                <button class="register-btn">ثبت نام</button>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="faq-section">
            <h2 class="faq-title">سوالات متداول</h2>
            
            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    <span>چگونه می‌توانم در سامانه رفاه کالا ثبت‌نام کنم؟</span>
                    <span class="icon">▼</span>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-content">
                        برای ثبت‌نام در سامانه رفاه کالا، کافی است روی دکمه "ثبت نام" کلیک کرده و فرم مربوطه را با اطلاعات صحیح تکمیل کنید. پس از تایید اطلاعات، کد پیگیری درخواست برای شما پیامک خواهد شد.
                    </div>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    <span>بسته‌های رفاهی شامل چه مواردی هستند؟</span>
                    <span class="icon">▼</span>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-content">
                        بسته‌های رفاهی ما شامل انواع مواد غذایی، محصولات بهداشتی، و کالاهای مورد نیاز روزمره خانواده می‌باشد. محتویات دقیق هر بسته در هنگام انتخاب نمایش داده می‌شود.
                    </div>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    <span>روش‌های پرداخت چگونه است؟</span>
                    <span class="icon">▼</span>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-content">
                        شما می‌توانید از روش‌های مختلف پرداخت استفاده کنید: پرداخت نقدی، پرداخت با کارت، پرداخت آنلاین و پرداخت اقساطی. تمامی روش‌ها ایمن و قابل اعتماد هستند.
                    </div>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    <span>چگونه می‌توانم سفارش خود را پیگیری کنم؟</span>
                    <span class="icon">▼</span>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-content">
                        می‌توانید با شماره تلفن 26206918-021 تماس بگیرید و با کد پیگیری که دریافت کرده اید سفارش خود را پیگیری کنید.
                    </div>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    <span>آیا امکان تحویل در منزل وجود دارد؟</span>
                    <span class="icon">▼</span>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-content">
                        بله، شما می‌توانید بین دو گزینه "تحویل حضوری" و "ارسال به آدرس" انتخاب کنید. در صورت انتخاب ارسال به آدرس، بسته شما در کمترین زمان ممکن به آدرس مورد نظر ارسال خواهد شد.
                    </div>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    <span>در صورت بروز مشکل با چه شماره‌ای تماس بگیرم؟</span>
                    <span class="icon">▼</span>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-content">
                        برای رفع هرگونه مشکل یا سوال، می‌توانید با شماره‌های 26206918-021 و 26206725-021 تماس بگیرید. کارشناسان ما آماده پاسخگویی به شما هستند.
                    </div>
                </div>
            </div>
        </section>
    </main>

    @include('refah.partials.footer')

    <script>
        function toggleFaq(element) {
            const answer = element.nextElementSibling;
            const icon = element.querySelector('.icon');
            
            // Close all other FAQ items
            document.querySelectorAll('.faq-question').forEach(q => {
                if (q !== element) {
                    q.classList.remove('active');
                    q.nextElementSibling.classList.remove('open');
                }
            });
            
            // Toggle current FAQ item
            element.classList.toggle('active');
            answer.classList.toggle('open');
        }
    </script>
</body>
</html>
