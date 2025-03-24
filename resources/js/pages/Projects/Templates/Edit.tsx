import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { type Project, type WaitlistTemplate } from '@/types/project';
import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Check, Paintbrush, Save } from 'lucide-react';
import { useState } from 'react';
import { WaitlistTemplatePreview } from '@/components/waitlist-template-preview';

interface TemplateEditProps {
  project: Project;
  template: WaitlistTemplate;
}

interface WaitlistTemplatePreviewProps {
  template: WaitlistTemplate;
  className?: string;
}

export default function TemplateEdit({ project, template }: TemplateEditProps) {
  const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Projects', href: '/projects' },
    { title: project.name, href: `/projects/${project.id}` },
    { title: 'Templates', href: `/projects/${project.id}/templates` },
    { title: 'Customize', href: `/projects/${project.id}/templates/${template.id}/edit` },
  ];

  const initialCustomizations = project.template_customizations || {};

  const { data, setData, patch, processing } = useForm({
    customizations: {
      // Merge default settings with any existing customizations
      heading: initialCustomizations.heading || template.structure.components.find(c => c.type === 'header')?.content || 'Join Our Waitlist',
      description: initialCustomizations.description || template.structure.components.find(c => c.type === 'text')?.content || 'Be the first to know when we launch!',
      buttonText: initialCustomizations.buttonText || template.structure.components.find(c => c.type === 'form')?.button?.text || 'Join Waitlist',
      backgroundColor: initialCustomizations.backgroundColor || template.structure.settings.backgroundColor || '#ffffff',
      textColor: initialCustomizations.textColor || template.structure.settings.textColor || '#333333',
      buttonColor: initialCustomizations.buttonColor || template.structure.settings.buttonColor || '#4f46e5',
      buttonTextColor: initialCustomizations.buttonTextColor || template.structure.settings.buttonTextColor || '#ffffff',
    },
    is_active: template.pivot?.is_active ?? true,
  });

  const [activeTab, setActiveTab] = useState<'content' | 'style'>('content');

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    patch(`/projects/${project.id}/templates/${template.id}`);
  };

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title={`${project.name} - Customize Template`} />

      <div className="mb-6 flex items-center justify-between p-4">
        <div>
          <h1 className="text-2xl font-bold tracking-tight">Customize Template</h1>
          <p className="text-muted-foreground">Personalize the {template.name} template for your waitlist</p>
        </div>
        <div className="flex gap-2">
          <Button variant="outline" asChild>
            <Link href={`/projects/${project.id}`}>
              <ArrowLeft className="mr-1 size-4" />
              Back to Project
            </Link>
          </Button>
          <Button type="submit" form="template-form" disabled={processing}>
            <Save className="mr-1 size-4" />
            {processing ? 'Saving...' : 'Save Changes'}
          </Button>
        </div>
      </div>

      <div className="grid gap-6 lg:grid-cols-5">
        <div className="lg:col-span-2">
          <Card>
            <CardHeader className="px-4 pb-0">
              <div className="flex space-x-1 overflow-x-auto rounded-lg border bg-card p-1">
                <Button
                  variant={activeTab === 'content' ? 'default' : 'ghost'}
                  size="sm"
                  onClick={() => setActiveTab('content')}
                  className="flex-1"
                >
                  <Check className="mr-1 size-4" /> Content
                </Button>
                <Button
                  variant={activeTab === 'style' ? 'default' : 'ghost'}
                  size="sm"
                  onClick={() => setActiveTab('style')}
                  className="flex-1"
                >
                  <Paintbrush className="mr-1 size-4" /> Style
                </Button>
              </div>
            </CardHeader>
            <form id="template-form" onSubmit={handleSubmit}>
              <CardContent className="p-4 pt-4">
                {activeTab === 'content' ? (
                  <div className="space-y-4">
                    <div className="space-y-2">
                      <Label htmlFor="heading">Heading</Label>
                      <Input
                        id="heading"
                        value={data.customizations.heading}
                        onChange={(e: React.ChangeEvent<HTMLInputElement>) =>
                          setData('customizations', {
                            ...data.customizations,
                            heading: e.target.value,
                          })
                        }
                        maxLength={100}
                      />
                    </div>
                    <div className="space-y-2">
                      <Label htmlFor="description">Description</Label>
                      <Input
                        id="description"
                        value={data.customizations.description}
                        onChange={(e: React.ChangeEvent<HTMLInputElement>) =>
                          setData('customizations', {
                            ...data.customizations,
                            description: e.target.value,
                          })
                        }
                        maxLength={200}
                      />
                    </div>
                    <div className="space-y-2">
                      <Label htmlFor="buttonText">Button Text</Label>
                      <Input
                        id="buttonText"
                        value={data.customizations.buttonText}
                        onChange={(e: React.ChangeEvent<HTMLInputElement>) =>
                          setData('customizations', {
                            ...data.customizations,
                            buttonText: e.target.value,
                          })
                        }
                        maxLength={30}
                      />
                    </div>
                  </div>
                ) : (
                  <div className="space-y-4">
                    <div className="space-y-2">
                      <Label htmlFor="backgroundColor">Background Color</Label>
                      <div className="flex gap-2">
                        <div
                          className="h-10 w-10 rounded-md border"
                          style={{ backgroundColor: data.customizations.backgroundColor }}
                          aria-hidden="true"
                        />
                        <Input
                          id="backgroundColor"
                          value={data.customizations.backgroundColor}
                          onChange={(e: React.ChangeEvent<HTMLInputElement>) =>
                            setData('customizations', {
                              ...data.customizations,
                              backgroundColor: e.target.value,
                            })
                          }
                          type="text"
                          pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
                          placeholder="#ffffff"
                        />
                      </div>
                    </div>
                    <div className="space-y-2">
                      <Label htmlFor="textColor">Text Color</Label>
                      <div className="flex gap-2">
                        <div
                          className="h-10 w-10 rounded-md border"
                          style={{ backgroundColor: data.customizations.textColor }}
                          aria-hidden="true"
                        />
                        <Input
                          id="textColor"
                          value={data.customizations.textColor}
                          onChange={(e: React.ChangeEvent<HTMLInputElement>) =>
                            setData('customizations', {
                              ...data.customizations,
                              textColor: e.target.value,
                            })
                          }
                          type="text"
                          pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
                          placeholder="#333333"
                        />
                      </div>
                    </div>
                    <div className="space-y-2">
                      <Label htmlFor="buttonColor">Button Color</Label>
                      <div className="flex gap-2">
                        <div
                          className="h-10 w-10 rounded-md border"
                          style={{ backgroundColor: data.customizations.buttonColor }}
                          aria-hidden="true"
                        />
                        <Input
                          id="buttonColor"
                          value={data.customizations.buttonColor}
                          onChange={(e: React.ChangeEvent<HTMLInputElement>) =>
                            setData('customizations', {
                              ...data.customizations,
                              buttonColor: e.target.value,
                            })
                          }
                          type="text"
                          pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
                          placeholder="#4f46e5"
                        />
                      </div>
                    </div>
                    <div className="space-y-2">
                      <Label htmlFor="buttonTextColor">Button Text Color</Label>
                      <div className="flex gap-2">
                        <div
                          className="h-10 w-10 rounded-md border"
                          style={{ backgroundColor: data.customizations.buttonTextColor }}
                          aria-hidden="true"
                        />
                        <Input
                          id="buttonTextColor"
                          value={data.customizations.buttonTextColor}
                          onChange={(e: React.ChangeEvent<HTMLInputElement>) =>
                            setData('customizations', {
                              ...data.customizations,
                              buttonTextColor: e.target.value,
                            })
                          }
                          type="text"
                          pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
                          placeholder="#ffffff"
                        />
                      </div>
                    </div>
                  </div>
                )}
              </CardContent>
            </form>
          </Card>
        </div>

        <div className="lg:col-span-3">
          <Card>
            <CardHeader className="border-b px-4 pb-3">
              <h3 className="text-sm font-medium">Preview</h3>
            </CardHeader>
            <CardContent className="h-[500px] max-h-[60vh] overflow-hidden rounded-b-lg">
              <WaitlistTemplatePreview 
                template={{
                  ...template,
                  structure: {
                    ...template.structure,
                    settings: {
                      ...template.structure.settings,
                      backgroundColor: data.customizations.backgroundColor,
                      textColor: data.customizations.textColor,
                      buttonColor: data.customizations.buttonColor,
                      buttonTextColor: data.customizations.buttonTextColor,
                    },
                    components: template.structure.components.map((component: any) => {
                      if (component.type === 'header') {
                        return {
                          ...component,
                          content: data.customizations.heading
                        };
                      } else if (component.type === 'text') {
                        return {
                          ...component,
                          content: data.customizations.description
                        };
                      } else if (component.type === 'form') {
                        return {
                          ...component,
                          button: {
                            ...component.button,
                            text: data.customizations.buttonText
                          }
                        };
                      }
                      return component;
                    })
                  }
                }}
                className="h-full w-full"
              />
            </CardContent>
          </Card>
        </div>
      </div>
    </AppLayout>
  );
}
