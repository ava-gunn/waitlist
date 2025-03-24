import { Card, CardContent } from '@/components/ui/card';
import { cn } from '@/lib/utils';
import { WaitlistTemplate } from '@/types/project';
import { useState } from 'react';

interface WaitlistTemplatePreviewProps {
  template: WaitlistTemplate;
  className?: string;
  customizations?: Record<string, any>;
}

interface ComponentProps {
  type: string;
  variant?: string;
  className?: string;
  level?: number;
  content?: string;
  children?: ComponentProps[];
  position?: string;
  fields?: FieldProps[];
  button?: ButtonProps;
  src?: string;
  alt?: string;
}

interface FieldProps {
  type: string;
  label?: string;
  placeholder?: string;
  required?: boolean;
}

interface ButtonProps {
  text: string;
  className?: string;
}

export function WaitlistTemplatePreview({ template, className, customizations = {} }: WaitlistTemplatePreviewProps) {
  const [loaded, setLoaded] = useState(false);

  const structure = template.structure || {};
  const settings = structure.settings || {};
  const components: ComponentProps[] = (structure.components || []) as ComponentProps[];

  // Helper function to render components recursively
  const renderComponent = (component: ComponentProps, index: number) => {
    switch (component.type) {
      case 'layout':
        if (component.variant === 'side-by-side' || component.variant === 'split-screen') {
          return (
            <div key={index} className="flex flex-col md:flex-row h-full">
              {component.children?.map((child, idx) => (
                <div 
                  key={idx} 
                  className={cn(
                    'flex-1',
                    child.position === 'left' && 'bg-muted',
                    child.className
                  )}
                  style={child.position === 'left' ? {
                    backgroundColor: customizations.backgroundColor || settings.backgroundColor || '#ffffff'
                  } : {}}
                >
                  {child.children ? child.children.map((c, i) => renderComponent(c, i)) : renderComponent(child, idx)}
                </div>
              ))}
            </div>
          );
        } else if (component.variant === 'centered') {
          return (
            <div key={index} className={cn("flex flex-col items-center justify-center p-6", component.className)}>
              {component.children?.map((child, idx) => renderComponent(child, idx))}
            </div>
          );
        }
        break;

      case 'content':
        return (
          <div key={index} className={cn("p-4", component.className)}>
            {component.children?.map((child, idx) => renderComponent(child, idx))}
          </div>
        );

      case 'header':
        // Handle dynamic header tags properly
        const level = component.level || 1;
        const headingContent = customizations.heading || component.content;
        const headingStyle = { color: customizations.textColor || settings.textColor || '#000000' };
        const headingClass = cn("font-bold", component.className);
        
        // Return the appropriate heading level
        switch(level) {
          case 1: return <h1 key={index} className={headingClass} style={headingStyle}>{headingContent}</h1>;
          case 2: return <h2 key={index} className={headingClass} style={headingStyle}>{headingContent}</h2>;
          case 3: return <h3 key={index} className={headingClass} style={headingStyle}>{headingContent}</h3>;
          case 4: return <h4 key={index} className={headingClass} style={headingStyle}>{headingContent}</h4>;
          case 5: return <h5 key={index} className={headingClass} style={headingStyle}>{headingContent}</h5>;
          case 6: return <h6 key={index} className={headingClass} style={headingStyle}>{headingContent}</h6>;
          default: return <h1 key={index} className={headingClass} style={headingStyle}>{headingContent}</h1>;
        }

      case 'text':
        return (
          <p 
            key={index} 
            className={cn("text-muted-foreground", component.className)}
            style={{ color: customizations.textColor || settings.textColor || '#000000' }}
          >
            {customizations.description || component.content}
          </p>
        );

      case 'form':
        return (
          <div key={index} className={cn("space-y-4", component.className)}>
            {component.fields?.map((field, idx) => (
              <div key={idx} className="space-y-2">
                {field.label && (
                  <label 
                    className="text-sm font-medium"
                    style={{ color: customizations.textColor || settings.textColor || '#000000' }}
                  >
                    {field.label}
                  </label>
                )}
                <input 
                  type={field.type} 
                  placeholder={field.placeholder} 
                  className="w-full rounded-md border border-input bg-background px-3 py-2 placeholder:text-muted-foreground" 
                  disabled 
                />
              </div>
            ))}
            {component.button && (
              <button 
                className={cn(
                  "inline-flex items-center justify-center whitespace-nowrap rounded-md font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring",
                  "px-4 py-2",
                  component.button.className
                )}
                style={{
                  backgroundColor: customizations.buttonColor || settings.buttonColor || '#4f46e5',
                  color: customizations.buttonTextColor || settings.buttonTextColor || '#ffffff',
                }}
                disabled
              >
                {customizations.buttonText || component.button.text}
              </button>
            )}
          </div>
        );

      case 'image':
        return (
          <div key={index} className="flex items-center justify-center p-4 h-full">
            {component.src ? (
              <img 
                src={component.src} 
                alt={component.alt || 'Template image'} 
                className="max-w-full max-h-full object-contain"
                onLoad={() => setLoaded(true)}
              />
            ) : (
              <div className="rounded-md bg-muted flex items-center justify-center w-full h-32 text-muted-foreground">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className="w-8 h-8">
                  <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                  <circle cx="9" cy="9" r="2"/>
                  <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                </svg>
              </div>
            )}
          </div>
        );

      case 'div':
        return (
          <div key={index} className={component.className}>
            {component.children?.map((child, idx) => renderComponent(child, idx))}
          </div>
        );

      default:
        return null;
    }
  };

  const previewStyle = {
    backgroundColor: customizations.backgroundColor || settings.backgroundColor || '#ffffff',
    color: customizations.textColor || settings.textColor || '#000000',
  };

  return (
    <Card className={cn("overflow-hidden transition-all h-[500px]", className)}>
      <CardContent className="p-0 h-full" style={previewStyle}>
        {!loaded && components.length > 0 && (
          <div className="w-full h-full flex items-center justify-center bg-muted/50">
            <div className="animate-pulse h-3/4 w-3/4 rounded-md bg-muted"></div>
          </div>
        )}
        <div className={cn("h-full overflow-hidden", !loaded && "hidden")}>
          {components.map((component, index) => renderComponent(component, index))}
        </div>
      </CardContent>
    </Card>
  );
}
