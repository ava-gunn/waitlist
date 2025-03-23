import { type Auth, type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { ArrowRight, CheckCircle2 } from 'lucide-react';

// Use the declaration from app.tsx, where route is declared as a global
declare const route: (name: string, params?: Record<string, any>) => string;

export default function Landing() {
  const { auth } = usePage<SharedData>().props;

  // Features list for the landing page
  const features = [
    'Easy waitlist creation',
    'Customizable templates',
    'Email verification',
    'Analytics dashboard',
    'Export waitlist data',
    'Manage multiple projects'
  ];

  return (
    <>
      <Head title="Waitlist Management System">
        <meta name="description" content="Create and manage professional waitlists for your products and services" />
        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
      </Head>

      <div className="flex min-h-screen flex-col bg-background font-sans">
        {/* Header */}
        <header className="sticky top-0 z-40 w-full border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
          <div className="mx-auto container max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 flex h-16 items-center justify-between">
            <div className="flex items-center gap-2">
              <svg className="h-8 w-8 text-primary" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2L2 7L12 12L22 7L12 2Z" fill="currentColor" />
                <path d="M2 17L12 22L22 17" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                <path d="M2 12L12 17L22 12" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
              </svg>
              <span className="text-xl font-bold">Waitlist</span>
            </div>
            <nav className="flex items-center gap-4">
              {auth.user ? (
                <Link href={route('dashboard')} className="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 h-10 px-4 py-2">
                  Go to Dashboard
                </Link>
              ) : (
                <>
                  <Link href={route('login')} className="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
                    Log in
                  </Link>
                  <Link href={route('register')} className="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 h-10 px-4 py-2">
                    Sign up
                  </Link>
                </>
              )}
            </nav>
          </div>
        </header>

        {/* Hero section */}
        <main className="mx-auto flex-1">
          <section className="py-12 md:py-24 lg:py-32">
            <div className="container max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">
              <div className="mx-auto flex max-w-[58rem] flex-col items-center space-y-4 text-center">
                <h1 className="text-4xl font-bold tracking-tighter sm:text-5xl md:text-6xl lg:text-7xl">
                  Collect and manage <span className="text-primary">waitlist signups</span> with ease
                </h1>
                <p className="max-w-[42rem] leading-normal text-muted-foreground sm:text-xl sm:leading-8">
                  Create beautiful waitlist pages for your product launches, manage signups, and analyze your growth - all in one platform.
                </p>
                <div className="flex flex-wrap justify-center gap-4">
                  {auth.user ? (
                    <Link href={route('dashboard')} className="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 h-11 rounded-md px-8">
                      Go to Dashboard <ArrowRight className="h-4 w-4" />
                    </Link>
                  ) : (
                    <>
                      <Link href={route('register')} className="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 h-11 rounded-md px-8">
                        Get Started <ArrowRight className="h-4 w-4" />
                      </Link>
                      <Link href={route('login')} className="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium border border-input bg-background hover:bg-accent hover:text-accent-foreground h-11 rounded-md px-8">
                        Log in to your account
                      </Link>
                    </>
                  )}
                </div>
              </div>
            </div>
          </section>

          {/* Features section */}
          <section className="py-12 md:py-24 lg:py-32">
            <div className="container max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">
              <div className="mx-auto grid max-w-5xl items-center gap-6 py-12 lg:grid-cols-2 lg:gap-12">
                <div className="space-y-4">
                  <h2 className="text-3xl font-bold tracking-tighter md:text-4xl">
                    Everything you need to collect signups
                  </h2>
                  <p className="text-muted-foreground">
                    Our platform provides all the tools you need to create, manage, and analyze your waitlist campaigns.
                  </p>
                  <ul className="grid gap-2">
                    {features.map((feature, index) => (
                      <li key={index} className="flex items-center gap-2">
                        <CheckCircle2 className="h-5 w-5 text-primary" />
                        <span>{feature}</span>
                      </li>
                    ))}
                  </ul>
                  <div className="pt-4">
                    <Link href={route('register')} className="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 h-10 px-4 py-2">
                      Get Started
                    </Link>
                  </div>
                </div>
                <div className="rounded-lg border bg-card p-8 shadow-sm">
                  <div className="space-y-4">
                    <div className="space-y-2">
                      <h3 className="text-xl font-bold">Join our waitlist</h3>
                      <p className="text-sm text-muted-foreground">
                        Sign up to be the first to know when we launch new features.
                      </p>
                    </div>
                    <div className="grid gap-4">
                      <div className="grid gap-2">
                        <label htmlFor="email" className="sr-only">Email</label>
                        <input
                          id="email"
                          type="email"
                          placeholder="Enter your email"
                          className="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        />
                      </div>
                      <Link href="#" className="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 h-11 rounded-md px-8">
                        Join the waitlist
                      </Link>
                    </div>
                    <p className="text-xs text-muted-foreground">
                      By signing up, you agree to our <a href="#" className="underline underline-offset-4">Terms of Service</a> and <a href="#" className="underline underline-offset-4">Privacy Policy</a>.
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </section>

          {/* CTA section */}
          <section className="bg-muted/40">
            <div className="container max-w-screen-xl mx-auto px-4 py-12 sm:px-6 md:py-24 lg:px-8 lg:py-32">
              <div className="mx-auto max-w-[58rem] space-y-6 text-center">
                <h2 className="text-3xl font-bold tracking-tighter md:text-4xl lg:text-5xl">
                  Start building your waitlist today
                </h2>
                <p className="text-muted-foreground md:text-xl">
                  Join thousands of creators and businesses using our platform to grow their audience.
                </p>
                <div className="flex flex-col justify-center gap-4 sm:flex-row">
                  {auth.user ? (
                    <Link href={route('dashboard')} className="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 h-11 rounded-md px-8">
                      Go to Dashboard
                    </Link>
                  ) : (
                    <>
                      <Link href={route('register')} className="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 h-11 rounded-md px-8">
                        Create your waitlist
                      </Link>
                      <Link href={route('login')} className="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium border border-input bg-background hover:bg-accent hover:text-accent-foreground h-11 rounded-md px-8">
                        Sign in
                      </Link>
                    </>
                  )}
                </div>
              </div>
            </div>
          </section>
        </main>

        {/* Footer */}
        <footer className=" border-t bg-muted/40 py-6 md:py-10">
          <div className="mx-auto container max-w-screen-xl mx-auto px-4 sm:px-6 md:flex-row lg:px-8 flex flex-col items-center justify-between gap-4">
            <div className="flex items-center gap-2">
              <svg className="h-6 w-6 text-primary" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2L2 7L12 12L22 7L12 2Z" fill="currentColor" />
                <path d="M2 17L12 22L22 17" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                <path d="M2 12L12 17L22 12" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
              </svg>
              <span className="font-semibold">Waitlist</span>
            </div>
            <p className="text-sm text-muted-foreground">
              &copy; {new Date().getFullYear()} Waitlist. All rights reserved.
            </p>
            <nav className="flex gap-4">
              <a href="#" className="text-sm text-muted-foreground underline-offset-4 hover:underline">Terms</a>
              <a href="#" className="text-sm text-muted-foreground underline-offset-4 hover:underline">Privacy</a>
              <a href="#" className="text-sm text-muted-foreground underline-offset-4 hover:underline">Contact</a>
            </nav>
          </div>
        </footer>
      </div>
    </>
  );
}
