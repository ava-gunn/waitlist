import { Button } from '@/components/ui/button';
import { Card, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { WaitlistTemplatePreview } from '@/components/waitlist-template-preview';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { type Project, type WaitlistTemplate } from '@/types/project';
import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Check } from 'lucide-react';

interface TemplatesIndexProps {
  project: Project;
  templates: WaitlistTemplate[];
}

export default function TemplatesIndex({ project, templates }: TemplatesIndexProps) {
  console.log({project, templates});
  const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Projects', href: '/projects' },
    { title: project.name, href: `/projects/${project.id}` },
    { title: 'Templates', href: `/projects/${project.id}/templates` },
  ];

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title={`${project.name} - Select Template`} />

      <div className="flex items-center justify-between p-4">
        <div>
          <h1 className="text-2xl font-bold tracking-tight">Select a Template</h1>
          <p className="text-muted-foreground">Choose a template for your waitlist landing page</p>
        </div>
        <Button variant="outline" asChild>
          <Link href={`/projects/${project.id}`}>
            <ArrowLeft className="mr-1 size-4" />
            Back to Project
          </Link>
        </Button>
      </div>

      <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3 p-4">
        {templates.map((template) => (
          <TemplateCard
            key={template.id}
            template={template}
            projectId={project.id}
            isSelected={project.waitlist_template_id === template.id}
          />
        ))}
      </div>
    </AppLayout>
  );
}

interface TemplateCardProps {
  template: WaitlistTemplate;
  projectId: number;
  isSelected: boolean;
}

function TemplateCard({ template, projectId, isSelected }: TemplateCardProps) {
  const { post } = useForm();

  return (
    <Card className={`overflow-hidden transition-all hover:shadow-md ${isSelected ? 'ring-2 ring-primary' : ''}`}>
      <WaitlistTemplatePreview
        template={template}
        className="border-b rounded-t-md"
      />
      <CardHeader className="p-4">
        <div className="flex items-center justify-between">
          <CardTitle className="line-clamp-1 text-lg">{template.name}</CardTitle>
          {isSelected && (
            <div className="flex h-6 w-6 items-center justify-center rounded-full bg-primary">
              <Check className="size-4 text-primary-foreground" />
            </div>
          )}
        </div>
        <CardDescription className="line-clamp-2">{template.description}</CardDescription>
      </CardHeader>
      <CardFooter className="border-t p-4">
        {isSelected ? (
          <div className="flex w-full gap-2">
            <Button variant="outline" size="sm" className="flex-1" asChild>
              <Link href={`/projects/${projectId}/templates/${template.id}/edit`}>
                Customize
              </Link>
            </Button>
            <Button
              variant="secondary"
              size="sm"
              className="flex-1"
              onClick={() => post(`/projects/${projectId}/templates/remove`)}
            >
              Remove
            </Button>
          </div>
        ) : (
          <Button className="w-full" onClick={() => post(`/projects/${projectId}/templates/${template.id}/set`)}>
            Select Template
          </Button>
        )}
      </CardFooter>
    </Card>
  );
}
