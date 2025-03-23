import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type Project, type WaitlistTemplate } from '@/types/project';
import { Head, useForm } from '@inertiajs/react';
import { MailCheck } from 'lucide-react';
import { useState } from 'react';

interface WaitlistPageProps {
  project: Project;
  template: WaitlistTemplate;
  customizations: Record<string, unknown>;
}

export default function WaitlistPage({ project, template, customizations }: WaitlistPageProps) {
  const [submitted, setSubmitted] = useState(false);

  // Merge template defaults with custom settings
  const settings = {
    backgroundColor: customizations.backgroundColor || template.structure.settings.backgroundColor,
    textColor: customizations.textColor || template.structure.settings.textColor,
    buttonColor: customizations.buttonColor || template.structure.settings.buttonColor,
    buttonTextColor: customizations.buttonTextColor || template.structure.settings.buttonTextColor,
    heading: customizations.heading || template.structure.components.find(c => c.type === 'header')?.content,
    description: customizations.description || template.structure.components.find(c => c.type === 'text')?.content,
    buttonText: customizations.buttonText || template.structure.components.find(c => c.type === 'form')?.button?.text,
  };

  const { data, setData, post, processing, errors, reset } = useForm({
    email: '',
    name: '',
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    post(`/signup/${project.subdomain}`, {
      onSuccess: () => {
        reset();
        setSubmitted(true);
      },
    });
  };

  return (
    <>
      <Head title={project.name}>
        <meta name="description" content={`Join the waitlist for ${project.name}`} />
      </Head>

      <div
        className="flex min-h-screen flex-col"
        style={{
          backgroundColor: settings.backgroundColor,
          color: settings.textColor,
        }}
      >
        <header className="p-4">
          {project.logo_path ? (
            <img 
              src={project.logo_path} 
              alt={`${project.name} logo`} 
              className="h-10 object-contain"
            />
          ) : (
            <div className="text-xl font-bold">{project.name}</div>
          )}
        </header>

        <main className="flex flex-1 items-center justify-center p-4">
          <div className="mx-auto w-full max-w-md">
            {!submitted ? (
              <div className="space-y-8">
                <div className="space-y-2 text-center">
                  <h1 
                    className="text-3xl font-bold" 
                    style={{ color: settings.textColor }}
                  >
                    {settings.heading}
                  </h1>
                  <p className="text-lg">{settings.description}</p>
                </div>

                <form onSubmit={handleSubmit} className="space-y-4">
                  <div className="space-y-2">
                    <Label 
                      htmlFor="email" 
                      className="block" 
                      style={{ color: settings.textColor }}
                    >
                      Email <span aria-hidden="true">*</span>
                    </Label>
                    <Input
                      id="email"
                      type="email"
                      value={data.email}
                      onChange={(e) => setData('email', e.target.value)}
                      required
                      placeholder="Enter your email"
                      autoComplete="email"
                      className="w-full"
                      aria-required="true"
                      aria-invalid={errors.email ? 'true' : 'false'}
                      aria-describedby={errors.email ? 'email-error' : undefined}
                    />
                    {errors.email && (
                      <p id="email-error" className="mt-1 text-sm font-medium text-destructive">
                        {errors.email}
                      </p>
                    )}
                  </div>

                  {project.settings?.collect_name && (
                    <div className="space-y-2">
                      <Label 
                        htmlFor="name" 
                        className="block" 
                        style={{ color: settings.textColor }}
                      >
                        Name
                      </Label>
                      <Input
                        id="name"
                        type="text"
                        value={data.name}
                        onChange={(e) => setData('name', e.target.value)}
                        placeholder="Enter your name"
                        autoComplete="name"
                        className="w-full"
                        aria-describedby={errors.name ? 'name-error' : undefined}
                      />
                      {errors.name && (
                        <p id="name-error" className="mt-1 text-sm font-medium text-destructive">
                          {errors.name}
                        </p>
                      )}
                    </div>
                  )}

                  <Button
                    type="submit"
                    className="w-full text-base"
                    disabled={processing}
                    style={{
                      backgroundColor: settings.buttonColor,
                      color: settings.buttonTextColor,
                    }}
                  >
                    {processing ? 'Processing...' : settings.buttonText}
                  </Button>
                </form>
              </div>
            ) : (
              <div className="space-y-6 rounded-xl border border-border bg-card/30 p-8 text-center shadow-sm">
                <div className="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-primary/10">
                  <MailCheck className="size-10 text-primary" />
                </div>
                <div className="space-y-2">
                  <h2 className="text-2xl font-bold">Thank you for signing up!</h2>
                  <p>
                    We've added you to our waitlist and will notify you when we launch.
                    Please check your email to confirm your subscription.
                  </p>
                </div>
                {project.settings?.social_sharing && (
                  <div className="space-y-2">
                    <p className="text-sm font-medium">Share with friends:</p>
                    <div className="flex justify-center space-x-3">
                      <Button 
                        variant="outline" 
                        onClick={() => {
                          window.open(
                            `https://twitter.com/intent/tweet?text=I just joined the waitlist for ${project.name}! Check it out:&url=${encodeURIComponent(window.location.href)}`,
                            '_blank'
                          );
                        }}
                        aria-label="Share on Twitter"
                      >
                        Twitter
                      </Button>
                      <Button 
                        variant="outline" 
                        onClick={() => {
                          window.open(
                            `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(window.location.href)}`,
                            '_blank'
                          );
                        }}
                        aria-label="Share on Facebook"
                      >
                        Facebook
                      </Button>
                    </div>
                  </div>
                )}
              </div>
            )}
          </div>
        </main>

        <footer 
          className="p-4 text-center text-sm" 
          style={{ color: `${settings.textColor}99` }}
        >
          <p>&copy; {new Date().getFullYear()} {project.name}. All rights reserved.</p>
        </footer>
      </div>
    </>
  );
}
