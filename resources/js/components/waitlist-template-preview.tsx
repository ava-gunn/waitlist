import { Card, CardContent } from '@/components/ui/card';
import { cn } from '@/lib/utils';
import { WaitlistTemplate } from '@/types/project';
import { useState } from 'react';

interface WaitlistTemplatePreviewProps {
  template: WaitlistTemplate;
  className?: string;
}

export function WaitlistTemplatePreview({ template, className }: WaitlistTemplatePreviewProps) {
  const [loaded, setLoaded] = useState(false);

  const structure = template.structure || {};
  const settings = structure.settings || {};
  const components = structure.components || [];

  // Helper function to render components recursively
  const renderComponent = (component: any, index: number) => {
    switch (component.type) {
      case 'layout':
        if (component.variant === 'side-by-side' || component.variant === 'split-screen') {
          return (
            <div key={index} className="flex flex-col md:flex-row h-full">
              {component.children?.map((child: any, idx: number) => (
                <div 
                  key={idx} 
                  className={cn(
                    'flex-1',
                    child.position === 'left' && 'bg-muted',
                    child.className
                  )}
                >
                  {child.children ? child.children.map((c: any, i: number) => renderComponent(c, i)) : renderComponent(child, idx)}
                </div>
              ))}
            </div>
          );
        } else if (component.variant === 'centered') {
          return (
            <div key={index} className={cn("flex flex-col items-center justify-center p-6", component.className)}>
              {component.children?.map((child: any, idx: number) => renderComponent(child, idx))}
            </div>
          );
        }
        break;

      case 'content':
        return (
          <div key={index} className={cn("p-4", component.className)}>
            {component.children?.map((child: any, idx: number) => renderComponent(child, idx))}
          </div>
        );

      case 'header':
        const HeaderTag = `h${component.level || 1}` as keyof JSX.IntrinsicElements;
        return (
          <HeaderTag key={index} className={cn("font-bold", component.className)}>
            {component.content}
          </HeaderTag>
        );

      case 'text':
        return (
          <p key={index} className={cn("text-muted-foreground", component.className)}>
            {component.content}
          </p>
        );

      case 'form':
        return (
          <div key={index} className={cn("space-y-4", component.className)}>
            {component.fields?.map((field: any, idx: number) => (
              <div key={idx} className="space-y-2">
                {field.label && (
                  <label className="text-sm font-medium">{field.label}</label>
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
                  "bg-primary text-primary-foreground hover:bg-primary/90 px-4 py-2",
                  component.button.className
                )}
                disabled
              >
                {component.button.text}
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
            {component.children?.map((child: any, idx: number) => renderComponent(child, idx))}
          </div>
        );

      default:
        return null;
    }
  };

  const previewStyle = {
    backgroundColor: settings.backgroundColor || '#ffffff',
    color: settings.textColor || '#000000',
  };

  return (
    <Card className={cn("overflow-hidden transition-all h-[300px]", className)}>
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
