import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { useToast } from "@/components/ui/use-toast";
import { router } from "@inertiajs/react";
import { Head } from "@inertiajs/react";
import { FormEvent, useState } from "react";

interface SignupProps {
  project: {
    id: number;
    name: string;
    subdomain: string;
    settings: Record<string, any>;
    description?: string;
  };
  template: {
    id: number;
    name: string;
    description?: string;
    structure: Record<string, any>;
    customizations?: Record<string, any>;
  };
}

export default function Signup({ project, template }: SignupProps) {
  const [formData, setFormData] = useState({
    email: "",
    name: "",
  });
  const [submitting, setSubmitting] = useState(false);
  const { toast } = useToast();

  // Extract primary color from project settings or use default
  const primaryColor = project.settings?.primary_color || "#0f172a";
  const secondaryColor = project.settings?.secondary_color || "#64748b";
  
  // Apply color scheme based on project settings
  const colorStyle = {
    backgroundColor: project.settings?.background_color || "#ffffff",
    color: project.settings?.text_color || "#333333",
    borderColor: primaryColor,
  };

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    setFormData((prev) => ({ ...prev, [name]: value }));
  };

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault();
    setSubmitting(true);

    try {
      router.post(`/signup/${project.subdomain}`, formData, {
        onSuccess: () => {
          toast({
            title: "Success!",
            description: "Thanks for signing up! Please check your email to verify your account.",
            duration: 5000,
          });
          // Clear form
          setFormData({ email: "", name: "" });
        },
        onError: () => {
          toast({
            title: "Error",
            description: "There was a problem submitting your information. Please try again.",
            variant: "destructive",
            duration: 5000,
          });
        },
        onFinish: () => setSubmitting(false)
      });
    } catch (error) {
      toast({
        title: "Error",
        description: "There was a problem submitting your information. Please try again.",
        variant: "destructive",
        duration: 5000,
      });
      setSubmitting(false);
    }
  };

  // Get customized title and description from template
  const title = template.customizations?.title || template.name || "Join our waitlist";
  const description = template.customizations?.description || template.description || "We'll notify you when we're ready.";

  return (
    <>
      <Head title={`${project.name} | Waitlist Signup`}>
        <meta name="description" content={description} />
      </Head>
      
      <div className="min-h-screen flex items-center justify-center p-4" style={colorStyle}>
        <Card className="w-full max-w-md shadow-lg border-t-4" style={{ borderTopColor: primaryColor }}>
          <CardHeader>
            <CardTitle className="text-2xl font-bold">{title}</CardTitle>
            <CardDescription>{description}</CardDescription>
          </CardHeader>
          
          <CardContent>
            <form onSubmit={handleSubmit} className="space-y-4">
              <div className="space-y-2">
                <Label htmlFor="email" className="text-sm font-medium">
                  Email
                </Label>
                <Input
                  id="email"
                  name="email"
                  type="email"
                  value={formData.email}
                  onChange={handleChange}
                  placeholder="name@example.com"
                  required
                  aria-required="true"
                  className="w-full"
                />
              </div>
              
              <div className="space-y-2">
                <Label htmlFor="name" className="text-sm font-medium">
                  Name
                </Label>
                <Input
                  id="name"
                  name="name"
                  type="text"
                  value={formData.name}
                  onChange={handleChange}
                  placeholder="Your name"
                  required
                  aria-required="true"
                  className="w-full"
                />
              </div>
              
              <Button 
                type="submit" 
                className="w-full" 
                disabled={submitting}
                aria-busy={submitting}
                style={{ backgroundColor: primaryColor }}
              >
                {submitting ? "Submitting..." : "Join Waitlist"}
              </Button>
            </form>
          </CardContent>
          
          <CardFooter className="flex justify-center text-xs text-gray-500 pt-2 pb-4">
            <p>Powered by Waitlist</p>
          </CardFooter>
        </Card>
      </div>
    </>
  );
}
