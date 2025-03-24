import { Head, Link, useForm } from '@inertiajs/react';
import { useState } from 'react';

interface Project {
  id: number;
  name: string;
  subdomain: string;
  logo_path: string | null;
  is_active: boolean;
  full_url: string;
}

interface WaitlistTemplate {
  id: number;
  name: string;
  description: string | null;
  content: string;
}

interface LandingProps {
  project: Project;
  template: WaitlistTemplate;
  customizations: Record<string, any>;
}

type FormData = {
  email: string;
  name: string;
};

export default function Landing({ project, template, customizations }: LandingProps) {
  const [submitted, setSubmitted] = useState(false);
  const [error, setError] = useState('');
  
  const { data, setData, post, processing } = useForm<FormData>({
    email: '',
    name: '',
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    
    post(route('waitlist.signup', { subdomain: project.subdomain }), {
      onSuccess: () => {
        setSubmitted(true);
      },
      onError: (errors: Record<string, string>) => {
        if (errors.email) {
          setError(errors.email);
        } else {
          setError('An error occurred. Please try again.');
        }
      }
    });
  };

  // Helper function to get customization or default value
  const getCustomization = (key: string, defaultValue: any = '') => {
    return customizations && customizations[key] !== undefined ? customizations[key] : defaultValue;
  };

  // Extract the JSON components from template content
  const templateComponents = template.content ? JSON.parse(template.content) : [];

  // Render each component based on its type
  const renderComponent = (component: any, index: number | string) => {
    switch (component.type) {
      case 'layout':
        return (
          <div 
            key={`component-${index}`}
            className={component.className || 'container mx-auto px-4 py-8'}
            style={component.style || {}}
          >
            {component.children && component.children.map((child: any, childIndex: number) => 
              renderComponent(child, `${index}-${childIndex}`)
            )}
          </div>
        );

      case 'header':
        const headingLevel = component.level || 1;
        const HeaderTag = `h${headingLevel}` as keyof React.JSX.IntrinsicElements;
        const headerText = getCustomization(`header-${index}`, component.content);
        return (
          <HeaderTag 
            key={`component-${index}`}
            className={component.className || 'text-3xl font-bold mb-4'}
            style={component.style || {}}
          >
            {headerText}
          </HeaderTag>
        );

      case 'text':
        const textContent = getCustomization(`text-${index}`, component.content);
        return (
          <p 
            key={`component-${index}`}
            className={component.className || 'mb-4 text-gray-700'}
            style={component.style || {}}
          >
            {textContent}
          </p>
        );

      case 'form':
        return (
          <div key={`component-${index}`} className="w-full max-w-md mx-auto">
            {submitted ? (
              <div className="p-4 bg-green-50 border border-green-200 rounded-lg text-center">
                <svg 
                  xmlns="http://www.w3.org/2000/svg" 
                  className="h-12 w-12 mx-auto text-green-500 mb-4" 
                  fill="none" 
                  viewBox="0 0 24 24" 
                  stroke="currentColor"
                >
                  <path 
                    strokeLinecap="round" 
                    strokeLinejoin="round" 
                    strokeWidth={2} 
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" 
                  />
                </svg>
                <h3 className="text-lg font-medium text-green-800">Thanks for signing up!</h3>
                <p className="mt-2 text-green-700">Please check your email to confirm your spot on the waitlist.</p>
              </div>
            ) : (
              <form onSubmit={handleSubmit} className="space-y-4">
                {component.collectName !== false && (
                  <div>
                    <label htmlFor="name" className="block text-sm font-medium text-gray-700 mb-1">
                      Name
                    </label>
                    <input
                      id="name"
                      type="text"
                      value={data.name}
                      onChange={(e) => setData('name', e.target.value)}
                      className="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"
                      placeholder="Your name"
                    />
                  </div>
                )}

                <div>
                  <label htmlFor="email" className="block text-sm font-medium text-gray-700 mb-1">
                    Email <span className="text-red-500">*</span>
                  </label>
                  <input
                    id="email"
                    type="email"
                    value={data.email}
                    onChange={(e) => setData('email', e.target.value)}
                    required
                    className="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"
                    placeholder="your@email.com"
                  />
                </div>

                {error && (
                  <div className="text-red-500 text-sm">{error}</div>
                )}

                <button
                  type="submit"
                  disabled={processing}
                  className="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary disabled:opacity-50"
                >
                  {processing ? 'Submitting...' : 'Join Waitlist'}
                </button>
              </form>
            )}
          </div>
        );

      case 'image':
        return (
          <div 
            key={`component-${index}`}
            className={component.className || 'my-4'}
          >
            <img 
              src={component.src} 
              alt={component.alt || 'Image'} 
              className="max-w-full h-auto"
              style={component.style || {}}
            />
          </div>
        );

      default:
        return null;
    }
  };

  // Get customized title or default to project name
  const pageTitle = getCustomization('title', `${project.name} Waitlist`);

  return (
    <>
      <Head>
        <title>{pageTitle}</title>
        <meta name="description" content={getCustomization('description', `Join the waitlist for ${project.name}`)} />
        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
      </Head>
      
      <div className="min-h-screen bg-white font-sans">
        {/* Header with Project Branding */}
        <header className="border-b border-gray-200 bg-white py-4">
          <div className="container mx-auto px-4 flex justify-between items-center">
            <div className="flex items-center space-x-2">
              {project.logo_path ? (
                <img src={project.logo_path} alt={`${project.name} logo`} className="h-8 w-auto" />
              ) : (
                <div className="h-8 w-8 bg-primary rounded-md flex items-center justify-center text-white font-bold">
                  {project.name.charAt(0)}
                </div>
              )}
              <span className="text-xl font-semibold">{project.name}</span>
            </div>
          </div>
        </header>

        {/* Main Content */}
        <main className="container mx-auto px-4 py-8">
          {templateComponents.map((component: any, index: number) => 
            renderComponent(component, index)
          )}
        </main>

        {/* Footer */}
        <footer className="border-t border-gray-200 bg-gray-50 py-6">
          <div className="container mx-auto px-4 text-center text-sm text-gray-500">
            &copy; {new Date().getFullYear()} {project.name}. All rights reserved.
          </div>
        </footer>
      </div>
    </>
  );
}
