@extends('layouts.public')

@section('content')
<main>
    <section class="hero">
        <div class="container hero-grid">
            <div class="hero-copy">
                <span class="eyebrow-pill"><span class="live-dot"></span> Now live at Harbour &amp; Vine</span>
                <h1>Renting,<br><em>made simple.</em></h1>
                <p class="hero-lede">One thoughtful place for your rent, repairs, documents and conversations. PropNest keeps residents informed and property teams in control.</p>
                <div class="hero-actions">
                    <a href="{{ route('login') }}" class="button button-dark">Explore the resident portal <x-icon name="arrow-right" :size="17" /></a>
                    <a href="#features" class="button button-plain">See what’s inside</a>
                </div>
                <div class="hero-proof">
                    <div class="avatar-stack">
                        <span class="avatar avatar-xs">AM</span>
                        <span class="avatar avatar-xs avatar-2">PS</span>
                        <span class="avatar avatar-xs avatar-3">DO</span>
                        <span class="avatar avatar-xs avatar-4">CB</span>
                    </div>
                    <div>
                        <div class="proof-stars">
                            <x-icon name="star" :size="13" />
                            <x-icon name="star" :size="13" />
                            <x-icon name="star" :size="13" />
                            <x-icon name="star" :size="13" />
                            <x-icon name="star" :size="13" />
                        </div>
                        <span>Trusted by residents across {{ max($propertyCount, 3) }} London buildings</span>
                    </div>
                </div>
            </div>

            <div class="hero-art" aria-hidden="true">
                <div class="hero-art-frame">
                    <svg class="hero-art-skyline" viewBox="0 0 560 420" fill="none" preserveAspectRatio="xMidYMax slice">
                        <defs>
                            <linearGradient id="sky" x1="280" y1="0" x2="280" y2="420" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#F2E8D5"/>
                                <stop offset="1" stop-color="#E6D3B4"/>
                            </linearGradient>
                        </defs>
                        <rect width="560" height="420" fill="url(#sky)"/>
                        <circle cx="420" cy="105" r="58" fill="#E9B66A" fill-opacity=".72"/>
                        <path d="M0 330h560" stroke="#C7B291" stroke-width="2"/>
                        <path d="M28 330V213l53-32 53 32v117" fill="#28594C"/>
                        <path d="M42 226h20v18H42zM70 226h20v18H70zM42 255h20v18H42zM70 255h20v18H70zM42 284h20v18H42zM70 284h20v18H70z" fill="#F5EBD8" fill-opacity=".55"/>
                        <path d="M154 330V164l70-40 70 40v166" fill="#1E4B40"/>
                        <path d="M172 184h24v24h-24zM207 184h24v24h-24zM242 184h24v24h-24zM172 223h24v24h-24zM207 223h24v24h-24zM242 223h24v24h-24zM172 262h24v24h-24zM207 262h24v24h-24zM242 262h24v24h-24z" fill="#F5EBD8" fill-opacity=".5"/>
                        <path d="M312 330V246l53-31 53 31v84" fill="#2E6456"/>
                        <path d="M328 262h20v20h-20zM360 262h20v20h-20zM328 292h20v20h-20zM360 292h20v20h-20z" fill="#F5EBD8" fill-opacity=".5"/>
                        <path d="M437 330V239l46-28 46 28v91" fill="#174238"/>
                        <path d="M452 255h18v20h-18zM481 255h18v20h-18zM452 286h18v20h-18zM481 286h18v20h-18z" fill="#E9B66A" fill-opacity=".55"/>
                        <path d="M0 330h560v90H0z" fill="#123A31"/>
                        <path d="M20 352h520" stroke="#F5EBD8" stroke-opacity=".14" stroke-width="2"/>
                    </svg>

                    <div class="hero-art-overlay"></div>

                    <div class="art-property-card">
                        <div class="art-property-top">
                            <span class="mini-label">Your home</span>
                            <span class="badge badge-green"><span class="badge-dot"></span>Active</span>
                        </div>
                        <strong>14 Willow Court</strong>
                        <span>Unit 4B · Islington, N1</span>
                        <div class="art-property-meta">
                            <span><x-icon name="key" :size="14" /> 2 bed</span>
                            <span><x-icon name="calendar" :size="14" /> Ends 4 Mar 2027</span>
                        </div>
                    </div>

                    <div class="art-float art-float-rent">
                        <span class="art-float-icon"><x-icon name="pound" :size="16" /></span>
                        <div>
                            <span class="mini-label">Rent due</span>
                            <strong>£1,850</strong>
                        </div>
                        <span class="art-float-status">1 Nov</span>
                    </div>

                    <div class="art-float art-float-job">
                        <span class="art-float-icon art-float-icon-clay"><x-icon name="wrench" :size="16" /></span>
                        <div>
                            <span class="mini-label">Kitchen tap</span>
                            <strong>Danny visiting tomorrow</strong>
                        </div>
                        <span class="art-tick"><x-icon name="check" :size="13" /></span>
                    </div>

                    <div class="art-float art-float-doc">
                        <span class="art-float-icon art-float-icon-gold"><x-icon name="file" :size="16" /></span>
                        <div>
                            <span class="mini-label">Document</span>
                            <strong>Tenancy agreement</strong>
                        </div>
                        <x-icon name="download" :size="16" />
                    </div>
                </div>
                <div class="hero-art-glow"></div>
            </div>
        </div>
    </section>

    <section class="public-strip">
        <div class="container strip-grid">
            <div><strong>6</strong><span>portals in one platform</span></div>
            <div><strong>24/7</strong><span>access to your home admin</span></div>
            <div><strong>0</strong><span>paper forms to chase</span></div>
            <div><strong>100%</strong><span>visibility of repairs</span></div>
        </div>
    </section>

    <section class="public-section" id="features">
        <div class="container">
            <div class="section-heading">
                <div>
                    <span class="eyebrow">Everything in its place</span>
                    <h2>Less admin. More home.</h2>
                </div>
                <p>PropNest brings the everyday parts of renting into one clear, calm space — for residents, property managers and landlords alike.</p>
            </div>

            <div class="feature-grid">
                <article class="feature-card feature-card-wide">
                    <span class="feature-icon"><x-icon name="card" :size="22" /></span>
                    <h3>Rent, without the guesswork</h3>
                    <p>See what’s due, what’s paid and what’s coming next. Download receipts and settle rent in a few taps.</p>
                    <div class="feature-visual feature-visual-rent">
                        <div class="mini-rent-row">
                            <span class="mini-rent-icon"><x-icon name="pound" :size="15" /></span>
                            <div><span>November rent</span><strong>£1,850.00</strong></div>
                            <span class="badge badge-amber"><span class="badge-dot"></span>Due</span>
                        </div>
                        <div class="mini-rent-row is-muted">
                            <span class="mini-rent-icon"><x-icon name="check" :size="15" /></span>
                            <div><span>October rent</span><strong>£1,850.00</strong></div>
                            <span class="mini-paid">Paid</span>
                        </div>
                    </div>
                </article>

                <article class="feature-card">
                    <span class="feature-icon feature-icon-clay"><x-icon name="wrench" :size="22" /></span>
                    <h3>Repairs that keep you posted</h3>
                    <p>Log an issue, add a photo and follow every update from triage to completion.</p>
                    <div class="feature-status-row">
                        <span class="status-pill is-done">Submitted</span>
                        <span class="status-pill is-done">Booked</span>
                        <span class="status-pill is-current">In progress</span>
                    </div>
                </article>

                <article class="feature-card">
                    <span class="feature-icon feature-icon-ocean"><x-icon name="folder" :size="22" /></span>
                    <h3>Your paperwork, sorted</h3>
                    <p>Tenancy agreements, certificates and statements kept together and always to hand.</p>
                    <div class="feature-file">
                        <span class="file-thumb"><x-icon name="file" :size="17" /></span>
                        <div><strong>Tenancy agreement</strong><span>PDF · 248 KB</span></div>
                        <x-icon name="download" :size="16" />
                    </div>
                </article>

                <article class="feature-card">
                    <span class="feature-icon feature-icon-gold"><x-icon name="message" :size="22" /></span>
                    <h3>One thread, no chasing</h3>
                    <p>Ask a question, request a change or flag a concern — with the whole history in one place.</p>
                    <div class="feature-message">
                        <span class="avatar avatar-xs">SB</span>
                        <p>“All booked in for Tuesday — we’ll keep you posted.”</p>
                    </div>
                </article>

                <article class="feature-card feature-card-wide feature-card-dark">
                    <div>
                        <span class="feature-icon feature-icon-light"><x-icon name="building" :size="22" /></span>
                        <h3>A portfolio view that makes sense</h3>
                        <p>Occupancy, arrears, rent collection and maintenance risk in a single operational picture for property teams and landlords.</p>
                        <a href="{{ route('login') }}" class="text-link text-link-light">See the manager workspace <x-icon name="arrow-up-right" :size="14" /></a>
                    </div>
                    <div class="portfolio-bars" aria-hidden="true">
                        <div class="bar-row"><span>Feb</span><div><i style="width: 58%"></i></div><strong>£12.4k</strong></div>
                        <div class="bar-row"><span>Mar</span><div><i style="width: 72%"></i></div><strong>£15.1k</strong></div>
                        <div class="bar-row"><span>Apr</span><div><i style="width: 66%"></i></div><strong>£14.2k</strong></div>
                        <div class="bar-row"><span>May</span><div><i style="width: 84%"></i></div><strong>£18.6k</strong></div>
                        <div class="bar-row"><span>Jun</span><div><i style="width: 94%"></i></div><strong>£20.3k</strong></div>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section class="public-section public-section-cream" id="how">
        <div class="container how-grid">
            <div class="how-copy">
                <span class="eyebrow">How it works</span>
                <h2>From keys to calm in three steps.</h2>
                <p>No new habits to learn. PropNest fits around the way tenancies already work, then takes the friction out of the everyday.</p>
                <a href="{{ route('register') }}" class="button button-dark">Create your account <x-icon name="arrow-right" :size="17" /></a>
            </div>
            <ol class="steps">
                <li>
                    <span class="step-number">01</span>
                    <div>
                        <h3>Activate your portal</h3>
                        <p>Your agency invites you, or you register with the email on your tenancy. Everything is linked securely to your home.</p>
                    </div>
                </li>
                <li>
                    <span class="step-number">02</span>
                    <div>
                        <h3>See the whole picture</h3>
                        <p>Rent status, contract dates, documents, announcements and open repairs are waiting on one dashboard.</p>
                    </div>
                </li>
                <li>
                    <span class="step-number">03</span>
                    <div>
                        <h3>Ask, pay, report — simply</h3>
                        <p>Raise a request, settle rent or download a receipt without digging through email threads.</p>
                    </div>
                </li>
            </ol>
        </div>
    </section>

    <section class="public-section" id="roles">
        <div class="container">
            <div class="section-heading">
                <div>
                    <span class="eyebrow">Built for the whole picture</span>
                    <h2>Everyone sees what they need.</h2>
                </div>
                <p>Six connected portals, one source of truth. Each role gets a focused view without losing the thread.</p>
            </div>

            <div class="role-grid">
                <a href="{{ route('login') }}" class="role-card">
                    <span class="role-icon"><x-icon name="home" :size="20" /></span>
                    <h3>Tenants</h3>
                    <p>Rent, repairs, documents and requests in one resident home.</p>
                    <span class="role-link">Open tenant portal <x-icon name="arrow-right" :size="14" /></span>
                </a>
                <a href="{{ route('login') }}" class="role-card">
                    <span class="role-icon role-icon-clay"><x-icon name="building" :size="20" /></span>
                    <h3>Property managers</h3>
                    <p>Properties, tenancies, arrears and maintenance queues.</p>
                    <span class="role-link">Open agency workspace <x-icon name="arrow-right" :size="14" /></span>
                </a>
                <a href="{{ route('login') }}" class="role-card">
                    <span class="role-icon role-icon-gold"><x-icon name="chart" :size="20" /></span>
                    <h3>Landlords</h3>
                    <p>Portfolio performance, occupancy and collection overview.</p>
                    <span class="role-link">Open landlord view <x-icon name="arrow-right" :size="14" /></span>
                </a>
                <a href="{{ route('login') }}" class="role-card">
                    <span class="role-icon role-icon-ocean"><x-icon name="tool" :size="20" /></span>
                    <h3>Contractors</h3>
                    <p>Assigned jobs, visit details and progress updates.</p>
                    <span class="role-link">Open job sheet <x-icon name="arrow-right" :size="14" /></span>
                </a>
            </div>
        </div>
    </section>

    <section class="demo-section">
        <div class="container demo-card">
            <div>
                <span class="eyebrow eyebrow-light">Try the demo</span>
                <h2>Step inside the resident portal.</h2>
                <p>Sign in as Alex Morgan at 14 Willow Court to explore a live tenancy, or use a manager, landlord or contractor account to see the other side.</p>
                <a href="{{ route('login') }}" class="button button-light">Sign in to the demo <x-icon name="arrow-right" :size="17" /></a>
            </div>
            <div class="demo-credentials">
                <span class="demo-credentials-title">Demo accounts · password <code>password</code></span>
                <div class="demo-account"><span>Tenant</span><strong>alex.morgan@propnest.test</strong></div>
                <div class="demo-account"><span>Manager</span><strong>manager@propnest.test</strong></div>
                <div class="demo-account"><span>Landlord</span><strong>landlord@propnest.test</strong></div>
                <div class="demo-account"><span>Contractor</span><strong>contractor@propnest.test</strong></div>
            </div>
        </div>
    </section>
</main>
@endsection
